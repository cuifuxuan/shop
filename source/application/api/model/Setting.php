<?php

namespace app\api\model;

use app\common\model\Setting as SettingModel;

/**
 * 系统设置模型
 * Class Setting
 * @package app\api\model
 */
class Setting extends SettingModel
{
    /**
     * 获取积分名称
     * @return string
     */
    public static function getPointsName()
    {
        return static::getItem('points')['points_name'];
    }

    /**
     * 获取微信订阅消息设置
     */
    public static function getSubmsg()
    {
        $data = [];
        foreach (static::getItem('submsg') as $groupName => $group) {
            foreach ($group as $itemName => $item) {
                $data[$groupName][$itemName]['template_id'] = $item['template_id'];
            }
        }
        return $data;
    }

}
