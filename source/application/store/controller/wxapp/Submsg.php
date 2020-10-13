<?php

namespace app\store\controller\wxapp;

use app\store\controller\Controller;
use app\store\model\Setting as SettingModel;
use app\store\service\wxapp\SubMsg as SubMsgService;

/**
 * 小程序订阅消息设置
 * Class Submsg
 * @package app\store\controller\wxapp
 */
class Submsg extends Controller
{
    /**
     * 小程序订阅消息设置
     * @return array|bool|mixed
     * @throws \think\exception\DbException
     */
    public function index()
    {
        if (!$this->request->isAjax()) {
            $values = SettingModel::getItem('submsg');
            return $this->fetch('index', compact('values'));
        }
        $model = new SettingModel;
        if ($model->edit('submsg', $this->postData('submsg'))) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($model->getError() ?: '操作失败');
    }

    /**
     * 一键添加订阅消息
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function shuttle()
    {
        $SubMsgService = new SubMsgService;
        if ($SubMsgService->shuttle()) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError($SubMsgService->getError() ?: '操作失败');
    }

}