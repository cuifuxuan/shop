<?php

namespace app\api\controller\live;

use app\api\controller\Controller;
use app\api\model\wxapp\LiveRoom as LiveRoomModel;

/**
 * 微信小程序直播列表
 * Class Room
 * @package app\api\controller\live
 */
class Room extends Controller
{
    /**
     * 获取直播间列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function lists()
    {
        $model = new LiveRoomModel;
        $list = $model->getList();
        return $this->renderSuccess(compact('list'));
    }
}