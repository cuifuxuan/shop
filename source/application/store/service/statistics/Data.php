<?php

namespace app\store\service\statistics;

use app\common\service\Basics;
use app\store\service\statistics\data\Survey;
use app\store\service\statistics\data\Trade7days;
use app\store\service\statistics\data\GoodsRanking;
use app\store\service\statistics\data\UserExpendRanking;

/**
 * 数据概况服务类
 * Class Data
 * @package app\store\service\statistics
 */
class Data extends Basics
{
    /**
     * 获取数据概况
     * @param null $startDate
     * @param null $endDate
     * @return array
     * @throws \think\Exception
     */
    public function getSurveyData($startDate = null, $endDate = null)
    {
        return (new Survey)->getSurveyData($startDate, $endDate);
    }

    /**
     * 近7日走势
     * @return array
     * @throws \think\Exception
     */
    public function getTransactionTrend()
    {
        return (new Trade7days)->getTransactionTrend();
    }

    /**
     * 商品销售榜
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsRanking()
    {
        return (new GoodsRanking)->getGoodsRanking();
    }

    /**
     * 用户消费榜
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function geUserExpendRanking()
    {
        return (new UserExpendRanking)->getUserExpendRanking();
    }

}