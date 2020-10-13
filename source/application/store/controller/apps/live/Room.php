<?php

namespace app\store\controller\apps\live;

use app\store\controller\Controller;
use app\store\model\wxapp\LiveRoom as LiveRoomModel;

/**
 * 小程序直播间管理
 * Class Room
 * @package app\store\controller\apps\live
 */
class Room extends Controller
{
    /**
     * 直播间列表页
     * @param string $search 检索词
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index($search = '')
    {
        $model = new LiveRoomModel;
        $list = $model->getList($search);
        return $this->fetch('index', compact('list'));
    }

    /**
     * 同步刷新直播间列表
     * @return array|bool
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function refresh()
    {
        $model = new LiveRoomModel;
        if ($model->refreshLiveList()) {
            return $this->renderSuccess('同步成功');
        }
        return $this->renderError($model->getError() ?: '同步失败');
    }

    /**
     * 修改直播间置顶状态
     * @param int $id
     * @param int $is_top
     * @return array|bool
     * @throws \think\exception\DbException
     */
    public function settop($id, $is_top)
    {
        // 直播间详情
        $model = LiveRoomModel::detail($id);
        if (!$model->setIsTop($is_top)) {
            return $this->renderError('操作失败');
        }
        return $this->renderSuccess('操作成功');
    }

}