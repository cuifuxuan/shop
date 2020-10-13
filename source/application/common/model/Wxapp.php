<?php

namespace app\common\model;

use think\Cache;
use app\common\exception\BaseException;

/**
 * 微信小程序模型
 * Class Wxapp
 * @package app\common\model
 */
class Wxapp extends BaseModel
{
    protected $name = 'wxapp';

    /**
     * 小程序页面
     * @return \think\model\relation\HasOne
     */
    public function diyPage()
    {
        return $this->hasOne('WxappPage');
    }

    /**
     * 获取小程序信息
     * @param int|null $wxappId
     * @return static|null
     * @throws \think\exception\DbException
     */
    public static function detail($wxappId = null)
    {
        return static::get($wxappId ?: []);
    }

    /**
     * 从缓存中获取小程序信息
     * @param int|null $wxappId 小程序id
     * @return array $data
     * @throws BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public static function getWxappCache($wxappId = null)
    {
        // 小程序id
        is_null($wxappId) && $wxappId = static::$wxapp_id;
        if (!$data = Cache::get("wxapp_{$wxappId}")) {
            // 获取小程序详情, 解除hidden属性
            $detail = self::detail($wxappId)->hidden([], true);
            if (empty($detail)) throw new BaseException(['msg' => '未找到当前小程序信息']);
            // 写入缓存
            $data = $detail->toArray();
            Cache::tag('cache')->set("wxapp_{$wxappId}", $data);
        }
        return $data;
    }

}
