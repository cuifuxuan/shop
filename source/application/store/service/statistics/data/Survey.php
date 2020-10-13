<?php

namespace app\store\service\statistics\data;

use app\common\library\helper;
use app\common\service\Basics as BasicsService;
use app\store\model\User as UserModel;
use app\store\model\Order as OrderModel;
use app\store\model\Goods as GoodsModel;
use app\store\model\recharge\Order as RechargeOrderModel;
use app\common\enum\order\Status as OrderStatusEnum;
use app\common\enum\order\PayStatus as PayStatusEnum;
use app\common\enum\recharge\order\PayStatus as RechargePayStatusEnum;

/**
 * 数据概况
 * Class Survey
 * @package app\store\service\statistics\data
 */
class Survey extends BasicsService
{
    /**
     * 获取数据概况
     * @param $startDate
     * @param $endDate
     * @return array
     * @throws \think\Exception
     */
    public function getSurveyData($startDate = null, $endDate = null)
    {
        return [
            // 用户数量
            'user_total' => $this->getUserTotal($startDate, $endDate),
            // 消费人数
            'consume_users' => $this->getConsumeUsers($startDate, $endDate),
            // 付款订单数
            'order_total' => $this->getOrderTotal($startDate, $endDate),
            // 付款订单总额
            'order_total_money' => $this->getOrderTotalMoney($startDate, $endDate),
            // 商品总量
            'goods_total' => $this->getGoodsTotal($startDate, $endDate),
            // 用户充值总额
            'recharge_total' => $this->getRechargeTotal($startDate, $endDate),
        ];
    }

    /**
     * 获取用户总量
     * @param null $startDate
     * @param null $endDate
     * @return string
     * @throws \think\Exception
     */
    private function getUserTotal($startDate = null, $endDate = null)
    {
        $model = new UserModel;
        if (!is_null($startDate) && !is_null($endDate)) {
            $model->where('create_time', '>=', strtotime($startDate))
                ->where('create_time', '<', strtotime($endDate) + 86400);
        }
        $value = $model->where('is_delete', '=', '0')->count();
        return number_format($value);
    }

    /**
     * 消费人数
     * @param null $startDate
     * @param null $endDate
     * @return string
     * @throws \think\Exception
     */
    public function getConsumeUsers($startDate = null, $endDate = null)
    {
        $model = new OrderModel;
        if (!is_null($startDate) && !is_null($endDate)) {
            $model->where('pay_time', '>=', strtotime($startDate))
                ->where('pay_time', '<', strtotime($endDate) + 86400);
        }
        $value = $model->field('user_id')
            ->where('pay_status', '=', PayStatusEnum::SUCCESS)
            ->where('order_status', '<>', OrderStatusEnum::CANCELLED)
            ->where('is_delete', '=', '0')
            ->group('user_id')
            ->count();
        return number_format($value);
    }

    /**
     * 获取订单总量
     * @param null $startDate
     * @param null $endDate
     * @return string
     * @throws \think\Exception
     */
    private function getOrderTotal($startDate = null, $endDate = null)
    {
        return number_format((new OrderModel)->getPayOrderTotal($startDate, $endDate));
    }

    /**
     * 付款订单总额
     * @param null $startDate
     * @param null $endDate
     * @return string
     */
    private function getOrderTotalMoney($startDate = null, $endDate = null)
    {
        return helper::number2((new OrderModel)->getOrderTotalPrice($startDate, $endDate));
    }

    /**
     * 获取商品总量
     * @param null $startDate
     * @param null $endDate
     * @return int|string
     * @throws \think\Exception
     */
    private function getGoodsTotal($startDate = null, $endDate = null)
    {
        $model = new GoodsModel;
        if (!is_null($startDate) && !is_null($endDate)) {
            $model->where('create_time', '>=', strtotime($startDate))
                ->where('create_time', '<', strtotime($endDate) + 86400);
        }
        $value = $model->where('is_delete', '=', 0)->count();
        return number_format($value);
    }

    /**
     * 用户充值总额
     * @param null $startDate
     * @param null $endDate
     * @return float|int
     */
    private function getRechargeTotal($startDate = null, $endDate = null)
    {
        $model = new RechargeOrderModel;
        if (!is_null($startDate) && !is_null($endDate)) {
            $model->where('pay_time', '>=', strtotime($startDate))
                ->where('pay_time', '<', strtotime($endDate) + 86400);
        }
        $value = $model->where('pay_status', '=', RechargePayStatusEnum::SUCCESS)
            ->sum('actual_money');
        return helper::number2($value);
    }

}