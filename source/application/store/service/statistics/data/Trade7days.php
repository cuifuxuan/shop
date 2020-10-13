<?php

namespace app\store\service\statistics\data;

use app\common\service\Basics as BasicsService;
use app\store\model\Order as OrderModel;
use app\common\library\helper;

/**
 * 近7日走势
 * Class Trade7days
 * @package app\store\service\statistics\data
 */
class Trade7days extends BasicsService
{
    /* @var OrderModel $GoodsModel */
    private $OrderModel;

    /**
     * 构造方法
     */
    public function __construct()
    {
        /* 初始化模型 */
        $this->OrderModel = new OrderModel;
    }

    /**
     * 近7日走势
     * @return array
     * @throws \think\Exception
     */
    public function getTransactionTrend()
    {
        // 最近七天日期
        $lately7days = $this->getLately7days();
        return [
            'date' => helper::jsonEncode($lately7days),
            'order_total' => helper::jsonEncode($this->getOrderTotalByDate($lately7days)),
            'order_total_price' => helper::jsonEncode($this->getOrderTotalPriceByDate($lately7days))
        ];
    }

    /**
     * 最近七天日期
     */
    private function getLately7days()
    {
        // 获取当前周几
        $date = [];
        for ($i = 0; $i < 7; $i++) {
            $date[] = date('Y-m-d', strtotime('-' . $i . ' days'));
        }
        return array_reverse($date);
    }

    /**
     * 获取订单总量 (指定日期)
     * @param $days
     * @return array
     * @throws \think\Exception
     */
    private function getOrderTotalByDate($days)
    {
        $data = [];
        foreach ($days as $day) {
            $data[] = $this->getOrderTotal($day);
        }
        return $data;
    }

    /**
     * 获取订单总量
     * @param null $day
     * @return string
     * @throws \think\Exception
     */
    private function getOrderTotal($day = null)
    {
        return number_format($this->OrderModel->getPayOrderTotal($day, $day));
    }

    /**
     * 获取某天的总销售额
     * @param null $day
     * @return string
     */
    private function getOrderTotalPrice($day = null)
    {
        return helper::number2($this->OrderModel->getOrderTotalPrice($day, $day));
    }

    /**
     * 获取订单总量 (指定日期)
     * @param $days
     * @return array
     */
    private function getOrderTotalPriceByDate($days)
    {
        $data = [];
        foreach ($days as $day) {
            $data[] = $this->getOrderTotalPrice($day);
        }
        return $data;
    }

}