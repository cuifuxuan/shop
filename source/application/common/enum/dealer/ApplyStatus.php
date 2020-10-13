<?php

namespace app\common\enum\dealer;

use app\common\enum\EnumBasics;

/**
 * 枚举类：分销商入驻审核状态
 * Class ApplyStatus
 * @package app\common\enum\dealer
 */
class ApplyStatus extends EnumBasics
{
    // 待审核
    const AUDIT_WAIT = 10;

    // 审核通过
    const AUDIT_PASS = 20;

    // 驳回
    const AUDIT_REJECT = 30;

    /**
     * 获取枚举类型值
     * @return array
     */
    public static function data()
    {
        return [
            self::AUDIT_WAIT => [
                'name' => '待审核',
                'value' => self::AUDIT_WAIT,
            ],
            self::AUDIT_PASS => [
                'name' => '审核通过',
                'value' => self::AUDIT_PASS,
            ],
            self::AUDIT_REJECT => [
                'name' => '驳回',
                'value' => self::AUDIT_REJECT,
            ],
        ];
    }

}