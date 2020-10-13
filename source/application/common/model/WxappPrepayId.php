<?php

namespace app\common\model;

use app\common\enum\OrderType as OrderTypeEnum;

/**
 * 小程序prepay_id模型
 * Class WxappPrepayId
 * @package app\common\model
 */
class WxappPrepayId extends BaseModel
{
    protected $name = 'wxapp_prepay_id';

    /**
     * prepay_id 详情
     * @param int $orderId
     * @param int $orderType 订单类型
     * @return array|false|\PDOStatement|string|\think\Model|static
     */
    public static function detail($orderId, $orderType = OrderTypeEnum::MASTER)
    {
        return (new static)->where('order_id', '=', $orderId)
            ->where('order_type', '=', $orderType)
            ->order(['create_time' => 'desc'])
            ->find();
    }

    /**
     * 记录prepay_id使用次数
     * @return int|true
     * @throws \think\Exception
     */
    public function updateUsedTimes()
    {
        return $this->setInc('used_times', 1);
    }

    /**
     * 更新prepay_id已付款状态
     * @param $orderId
     * @param $orderType
     * @return false|int
     */
    public static function updatePayStatus($orderId, $orderType = OrderTypeEnum::MASTER)
    {
        // 获取prepay_id记录
        $model = static::detail($orderId, $orderType);
        if (empty($model)) {
            return false;
        }
        // 更新记录
        return $model->save(['can_use_times' => 3, 'pay_status' => 1]);
    }

}