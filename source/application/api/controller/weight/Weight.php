<?php

namespace app\api\controller\weight;

use app\api\controller\Controller;
use app\api\model\bsy\BsyWeight as BsyWeightModel;

/**
 * 自提订单管理
 * Class Order
 * @package app\api\controller\shop
 */
class Weight extends Controller
{
    /* @var \app\api\model\User $user */
    private $user;

    /**
     * 构造方法
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->user = $this->getUser();   // 用户信息
    }

    /**
     * 核销订单详情
     * @param $order_id
     * @param int $order_type
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function detail()
    {
        // 订单详情
        $model = new BsyWeightModel;
        $info = $model->getweightinfo($this->user['user_id']);
        return $this->renderSuccess($info);
    }

}