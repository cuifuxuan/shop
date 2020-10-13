<?php

namespace app\store\controller\data;

use app\store\controller\Controller;
use app\store\model\store\Shop as ShopModel;

class Shop extends Controller
{
    /* @var ShopModel $model */
    private $model;

    /**
     * 构造方法
     * @throws \app\common\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new ShopModel;
        $this->view->engine->layout(false);
    }

    /**
     * 门店列表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function lists()
    {
        $list = $this->model->getList($this->request->get());
        return $this->fetch('list', compact('list'));
    }

}