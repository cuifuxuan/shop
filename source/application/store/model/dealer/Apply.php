<?php

namespace app\store\model\dealer;

use app\common\model\dealer\Apply as ApplyModel;
use app\common\service\Message as MessageService;
use app\common\enum\dealer\ApplyStatus as ApplyStatusEnum;

/**
 * 分销商入驻申请模型
 * Class Apply
 * @package app\store\model\dealer
 */
class Apply extends ApplyModel
{
    /**
     * 获取分销商申请列表
     * @param string $search
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($search = '')
    {
        // 构建查询规则
        $this->alias('apply')
            ->field('apply.*, user.nickName, user.avatarUrl')
            ->with(['referee'])
            ->join('user', 'user.user_id = apply.user_id')
            ->order(['apply.create_time' => 'desc']);
        // 查询条件
        !empty($search) && $this->where('user.nickName|apply.real_name|apply.mobile', 'like', "%$search%");
        // 获取列表数据
        return $this->paginate(15, false, [
            'query' => \request()->request()
        ]);
    }

    /**
     * 分销商入驻审核
     * @param $data
     * @return bool
     */
    public function submit($data)
    {
        if ($data['apply_status'] == ApplyStatusEnum::AUDIT_REJECT && empty($data['reject_reason'])) {
            $this->error = '请填写驳回原因';
            return false;
        }
        $this->transaction(function () use ($data) {
            if ($data['apply_status'] == ApplyStatusEnum::AUDIT_PASS) {
                // 新增分销商用户
                User::add($this['user_id'], [
                    'real_name' => $this['real_name'],
                    'mobile' => $this['mobile'],
                    'referee_id' => $this['referee_id'],
                ]);
            }
            // 更新申请记录
            $data['audit_time'] = time();
            $this->allowField(true)->save($data);
            // 发送订阅消息
            MessageService::send('dealer.apply', [
                'apply' => $this,               // 申请记录
                'user' => $this['user'],        // 用户信息
            ]);
        });
        return true;
    }

}