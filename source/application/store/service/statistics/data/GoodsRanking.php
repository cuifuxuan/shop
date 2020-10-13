<?php

namespace app\store\service\statistics\data;

use app\common\service\Basics as BasicsService;
use app\store\model\OrderGoods as OrderGoodsModel;
use app\common\enum\order\Status as OrderStatusEnum;
use app\common\enum\order\PayStatus as OrderPayStatusEnum;

/**
 * 数据统计-商品销售榜
 * Class GoodsRanking
 * @package app\store\service\statistics\data
 */
class GoodsRanking extends BasicsService
{
    /**
     * 商品销售榜
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsRanking()
    {
        return (new OrderGoodsModel)->alias('o_goods')
            ->field([
                'goods_id',
                'goods_name',
                'SUM(total_pay_price) AS sales_volume',
                'SUM(total_num) AS total_sales_num'
            ])
            ->join('order', 'order.order_id = o_goods.order_id')
            ->where('order.pay_status', '=', OrderPayStatusEnum::SUCCESS)
            ->where('order.order_status', '<>', OrderStatusEnum::CANCELLED)
            ->group('goods_id, goods_name')
            // order：此处按总销售额排序，如需按销量改为total_sales_num
            ->order(['sales_volume' => 'DESC'])
            ->limit(10)
            ->select();
    }

}