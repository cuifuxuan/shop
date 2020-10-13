<?php

namespace app\api\model\sharing;

use app\api\model\Setting as SettingModel;
use app\common\model\sharing\Setting as SharingSettingModel;

/**
 * 拼团设置模型
 * Class Setting
 * @package app\api\model\sharing
 */
class Setting extends SharingSettingModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'update_time',
    ];

    public static function getSetting()
    {
        // 订阅消息
        $submsgList = [];
        foreach (SettingModel::getItem('submsg')['sharing'] as $key => $item) {
            $submsgList[$key] = $item['template_id'];
        }
        return [
            // 基础设置
            'basic' => static::getItem('basic'),
            // 订阅消息
            'order_submsg' => $submsgList,
        ];
    }

}