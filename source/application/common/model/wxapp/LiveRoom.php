<?php

namespace app\common\model\wxapp;

use app\common\model\BaseModel;

/**
 * 微信小程序直播间模型
 * Class LiveRoom
 * @package app\common\model\wxapp
 */
class LiveRoom extends BaseModel
{
    protected $name = 'wxapp_live_room';

    /**
     * 获取器: 开播时间
     * @param $value
     * @return false|string
     */
    public function getStartTimeAttr($value)
    {
        return \format_time($value);
    }

    /**
     * 获取器: 结束时间
     * @param $value
     * @return false|string
     */
    public function getEndTimeAttr($value)
    {
        return \format_time($value);
    }

    /**
     * 获取直播间详情
     * @param int $id
     * @return static|null
     * @throws \think\exception\DbException
     */
    public static function detail($id)
    {
        return static::get($id);
    }

}