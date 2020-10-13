<?php

namespace app\common\service;

/**
 * 消息通知服务
 * Class Message
 * @package app\common\service
 */
class Message extends Basics
{
    /**
     * 场景列表
     * [场景名称] => [场景类]
     * @var array
     */
    private static $sceneList = [
        // 订单支付成功
        'order.payment' => 'app\common\service\message\order\Payment',
        // 订单发货
        'order.delivery' => 'app\common\service\message\order\Delivery',
        // 订单退款
        'order.refund' => 'app\common\service\message\order\Refund',

        // 拼团进度通知
        'sharing.active_status' => 'app\common\service\message\sharing\ActiveStatus',

        // 分销商入驻通知
        'dealer.apply' => 'app\common\service\message\dealer\Apply',
        // 分销商提现通知
        'dealer.withdraw' => 'app\common\service\message\dealer\Withdraw',
    ];

    /**
     * 发送消息通知
     * @param string $sceneName 场景名称
     * @param array $param 参数
     * @return bool
     */
    public static function send($sceneName, $param)
    {
        if (!isset(self::$sceneList[$sceneName]))
            return false;
        $className = self::$sceneList[$sceneName];
        return class_exists($className) ? (new $className)->send($param) : false;
    }

}