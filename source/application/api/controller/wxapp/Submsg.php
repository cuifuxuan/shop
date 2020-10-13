<?php

namespace app\api\controller\wxapp;

use app\api\controller\Controller;
use app\api\model\Setting as SettingModel;

/**
 * 微信小程序订阅消息
 * Class Submsg
 * @package app\api\controller\wxapp
 */
class Submsg extends Controller
{
    /**
     * 获取订阅消息配置
     * @return array
     */
    public function setting()
    {
        $setting = SettingModel::getSubmsg();
        return $this->renderSuccess(compact('setting'));
    }

}