<?php

namespace app\common\enum\sharing;

use app\common\enum\EnumBasics;

/**
 * 拼团拼单状态
 * Class ActiveStatus
 * @package app\common\enum\sharing
 */
class ActiveStatus extends EnumBasics
{
    // 未拼单
    const ACTIVE_STATE_NORMAL = 0;

    // 拼单中
    const ACTIVE_STATE_BEGIN = 10;

    // 拼单成功
    const ACTIVE_STATE_SUCCESS = 20;

    // 拼单失败
    const ACTIVE_STATE_FAIL = 30;

    /**
     * 获取枚举数据
     * @return array
     */
    public static function data()
    {
        return [
            self::ACTIVE_STATE_NORMAL => [
                'name' => '未拼单',
                'value' => self::ACTIVE_STATE_NORMAL,
            ],
            self::ACTIVE_STATE_BEGIN => [
                'name' => '拼单中',
                'value' => self::ACTIVE_STATE_BEGIN,
            ],
            self::ACTIVE_STATE_SUCCESS => [
                'name' => '拼单成功',
                'value' => self::ACTIVE_STATE_SUCCESS,
            ],
            self::ACTIVE_STATE_FAIL => [
                'name' => '拼单失败',
                'value' => self::ACTIVE_STATE_FAIL,
            ],
        ];
    }
}