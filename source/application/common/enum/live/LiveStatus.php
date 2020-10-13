<?php

namespace app\common\enum\live;

use app\common\enum\EnumBasics;

/**
 * 微信小程序直播间状态枚举类
 * Class Room
 * @package app\common\enum\live
 */
class LiveStatus extends EnumBasics
{
    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            101 => [
                'name' => '直播中',
                'value' => 101,
            ],
            102 => [
                'name' => '未开始',
                'value' => 102,
            ],
            103 => [
                'name' => '已结束',
                'value' => 103,
            ],
            104 => [
                'name' => '禁播',
                'value' => 104,
            ],
            105 => [
                'name' => '暂停中',
                'value' => 105,
            ],
            106 => [
                'name' => '异常',
                'value' => 106,
            ],
            107 => [
                'name' => '已过期',
                'value' => 107,
            ],
        ];
    }

}