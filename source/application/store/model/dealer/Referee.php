<?php

namespace app\store\model\dealer;

use app\common\model\dealer\Referee as RefereeModel;

/**
 * 分销商推荐关系模型
 * Class Referee
 * @package app\store\model\dealer
 */
class Referee extends RefereeModel
{
    /**
     * 获取下级团队成员ID集
     * @param $dealerId
     * @param int $level
     * @return array
     */
    public function getTeamUserIds($dealerId, $level = -1)
    {
        $level > -1 && $this->where('m.level', '=', $level);
        return $this->alias('m')
            ->join('user', 'user.user_id = m.user_id')
            ->where('m.dealer_id', '=', $dealerId)
            ->where('user.is_delete', '=', 0)
            ->column('m.user_id');
    }

    /**
     * 获取指定用户的推荐人列表
     * @param $userId
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getRefereeList($userId)
    {
        return (new static)->with(['dealer1'])->where('user_id', '=', $userId)->select();
    }

    /**
     * 清空下级成员推荐关系
     * @param $dealerId
     * @param int $level
     * @return int
     */
    public function onClearTeam($dealerId, $level = -1)
    {
        $level > -1 && $this->where('level', '=', $level);
        return $this->where('dealer_id', '=', $dealerId)->delete();
    }

    /**
     * 清空上级推荐关系
     * @param $userId
     * @param int $level
     * @return int
     */
    public function onClearReferee($userId, $level = -1)
    {
        $level > -1 && $this->where('level', '=', $level);
        return $this->where('user_id', '=', $userId)->delete();
    }

    /**
     * 清空2-3级推荐人的关系记录
     * @param $teamIds
     * @return int
     */
    public function onClearTop($teamIds)
    {
        return $this->where('user_id', 'in', $teamIds)
            ->where('level', 'in', [2, 3])
            ->delete();
    }

}