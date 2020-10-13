<?php

namespace app\store\model\dealer;

use app\store\model\dealer\Referee as RefereeModel;
use app\common\model\dealer\User as UserModel;

/**
 * 分销商用户模型
 * Class User
 * @package app\store\model\dealer
 */
class User extends UserModel
{
    /**
     * 获取分销商用户列表
     * @param string $search
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($search = '')
    {
        // 构建查询规则
        $this->alias('dealer')
            ->field('dealer.*, user.nickName, user.avatarUrl')
            ->with(['referee'])
            ->join('user', 'user.user_id = dealer.user_id')
            ->where('dealer.is_delete', '=', 0)
            ->order(['dealer.create_time' => 'desc']);
        // 查询条件
        !empty($search) && $this->where('user.nickName|dealer.real_name|dealer.mobile', 'like', "%$search%");
        // 获取列表数据
        return $this->paginate(15, false, [
            'query' => \request()->request()
        ]);
    }

    /**
     * 编辑分销商用户
     * @param $data
     * @return bool
     */
    public function edit($data)
    {
        return $this->allowField(true)->save($data) !== false;
    }

    /**
     * 删除分销商用户
     * @return mixed
     */
    public function setDelete()
    {
        return $this->transaction(function () {
            // 获取一级团队成员ID集
            $RefereeModel = new RefereeModel;
            $team1Ids = $RefereeModel->getTeamUserIds($this['user_id'], 1);
            if (!empty($team1Ids)) {
                // 一级团队成员归属到平台
                $this->setFromplatform($team1Ids);
                // 一级推荐人ID
                $referee1Id = RefereeModel::getRefereeUserId($this['user_id'], 1, true);
                if ($referee1Id > 0) {
                    // 一级推荐人的成员数量(二级)
                    $this->setDecTeamNum($referee1Id, 2, count($team1Ids));
                    // 一级推荐人的成员数量(三级)
                    $team2Ids = $RefereeModel->getTeamUserIds($this['user_id'], 2);
                    !empty($team2Ids) && $this->setDecTeamNum($referee1Id, 3, count($team2Ids));
                    // 二级推荐人的成员数量(三级)
                    $referee2Id = RefereeModel::getRefereeUserId($this['user_id'], 2, true);
                    $referee2Id > 0 && $this->setDecTeamNum($referee2Id, 3, count($team1Ids));
                    // 清空分销商下级成员与上级推荐人的关系记录
                    $RefereeModel->onClearTop(array_merge($team1Ids, $team2Ids));
                }
            }
            // 清空下级推荐记录
            $RefereeModel->onClearTeam($this['user_id']);
            // 标记当前分销商记录为已删除
            return $this->delete();
        });
    }

    /**
     * 一级团队成员归属到平台
     * @param $userIds
     * @return false|int
     */
    private function setFromplatform($userIds)
    {
        return $this->isUpdate(true)->save(
            ['referee_id' => 0],
            ['user_id' => ['in', $userIds], 'is_delete' => 0]
        );
    }

    /**
     * 删除用户的上级推荐关系
     * @param $userId
     * @return bool
     * @throws \think\Exception
     */
    public function onDeleteReferee($userId)
    {
        // 获取推荐人列表
        $list = RefereeModel::getRefereeList($userId);
        if (!$list->isEmpty()) {
            // 递减推荐人的下级成员数量
            foreach ($list as $item) {
                $item['dealer1'] && $this->setDecTeamNum($item['dealer_id'], $item['level'], 1);
            }
            // 清空上级推荐关系
            (new RefereeModel)->onClearReferee($userId);
        }
        return true;
    }

    /**
     * 递减分销商成员数量
     * @param $dealerId
     * @param $level
     * @param $number
     * @return int|true
     * @throws \think\Exception
     */
    private function setDecTeamNum($dealerId, $level, $number)
    {
        $field = [1 => 'first_num', 2 => 'second_num', 3 => 'third_num'];
        return $this->where('user_id', '=', $dealerId)
            ->where('is_delete', '=', 0)
            ->setDec($field[$level], $number);
    }

    /**
     * 提现打款成功：累积提现佣金
     * @param $user_id
     * @param $money
     * @return false|int
     * @throws \think\exception\DbException
     */
    public static function totalMoney($user_id, $money)
    {
        $model = self::detail($user_id);
        return $model->save([
            'freeze_money' => $model['freeze_money'] - $money,
            'total_money' => $model['total_money'] + $money,
        ]);
    }

    /**
     * 提现驳回：解冻分销商资金
     * @param $user_id
     * @param $money
     * @return false|int
     * @throws \think\exception\DbException
     */
    public static function backFreezeMoney($user_id, $money)
    {
        $model = self::detail($user_id);
        return $model->save([
            'money' => $model['money'] + $money,
            'freeze_money' => $model['freeze_money'] - $money,
        ]);
    }

}