<?php

namespace app\api\service\order\source;

use app\common\service\Basics;
use app\common\enum\order\OrderSource as OrderSourceEnum;

/**
 * 商品库存工厂类
 * Class Factory
 * @package app\common\service\stock
 */
class Factory extends Basics
{
    // 订单来源的结算台服务类
    private static $class = [
        OrderSourceEnum::MASTER => 'Master',
        OrderSourceEnum::BARGAIN => 'Bargain',
        OrderSourceEnum::SHARP => 'Sharp',
    ];

    /**
     * 根据订单来源获取商品库存类
     * @param int $orderSource
     * @return mixed
     */
    public static function getFactory($orderSource = OrderSourceEnum::MASTER)
    {
        static $classObj = [];
        if (!isset($classObj[$orderSource])) {
            $className = __NAMESPACE__ . '\\' . static::$class[$orderSource];
            $classObj[$orderSource] = new $className();
        }
        return $classObj[$orderSource];
    }
}