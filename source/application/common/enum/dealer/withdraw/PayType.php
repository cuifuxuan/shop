<?php

namespace app\common\enum\dealer\withdraw;

use app\common\enum\EnumBasics;

/**
 * 枚举类：分销商提现打款方式
 * Class PayType
 * @package app\common\enum\dealer\withdraw
 */
class PayType extends EnumBasics
{
    // 微信
    const WECHAT = 10;

    // 支付宝
    const ALIPAY = 20;

    // 银行卡
    const BANK_CARD = 30;

    /**
     * 获取枚举类型值
     * @return array
     */
    public static function data()
    {
        return [
            self::WECHAT => [
                'name' => '微信',
                'value' => self::WECHAT,
            ],
            self::ALIPAY => [
                'name' => '支付宝',
                'value' => self::ALIPAY,
            ],
            self::BANK_CARD => [
                'name' => '银行卡',
                'value' => self::BANK_CARD,
            ]
        ];
    }

}
