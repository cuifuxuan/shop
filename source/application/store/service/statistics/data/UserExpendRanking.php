<?php

namespace app\store\service\statistics\data;

use app\store\model\User as UserModel;
use app\common\service\Basics as BasicsService;

/**
 * 数据统计-用户消费榜
 * Class UserExpendRanking
 * @package app\store\service\statistics\data
 */
class UserExpendRanking extends BasicsService
{
    /**
     * 用户消费榜
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserExpendRanking()
    {
        return (new UserModel)->field(['user_id', 'nickName', 'expend_money'])
            ->where('is_delete', '=', 0)
            ->order(['expend_money' => 'DESC'])
            ->limit(10)
            ->select();
    }

}