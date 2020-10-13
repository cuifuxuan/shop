<?php

namespace app\api\controller\sharing;

use app\api\controller\Controller;
use app\api\model\sharing\Setting as SettingModel;

/**
 * 拼团设置控制器
 * Class Setting
 * @package app\api\controller\sharing
 */
class Setting extends Controller
{
    /**
     * 获取拼团设置
     * @return array
     */
    public function getAll()
    {
        // 获取拼团设置
        $setting = SettingModel::getSetting();
        return $this->renderSuccess(compact('setting'));
    }

}
