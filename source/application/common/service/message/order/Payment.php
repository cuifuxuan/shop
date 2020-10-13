<?php

namespace app\common\service\message\order;

use app\common\service\message\Basics;
use app\common\model\Setting as SettingModel;
use app\common\enum\OrderType as OrderTypeEnum;

/**
 * 消息通知服务 [订单支付成功]
 * Class Payment
 * @package app\common\service\message\order
 */
class Payment extends Basics
{
    /**
     * 参数列表
     * @var array
     */
    protected $param = [
        'order' => [],
        'order_type' => OrderTypeEnum::MASTER,
    ];

    /**
     * 订单页面链接
     * @var array
     */
    private $pageUrl = [
        OrderTypeEnum::MASTER => 'pages/order/detail',
        OrderTypeEnum::SHARING => 'pages/sharing/order/detail/detail',
    ];

    /**
     * 发送消息通知
     * @param array $param
     * @return mixed|void
     * @throws \think\Exception
     */
    public function send($param)
    {
        // 记录参数
        $this->param = $param;
        // 短信通知商家
        $this->onSendSms();
        // 微信订阅消息通知用户
        $this->onSendWxSubMsg();
    }

    /**
     * 短信通知商家
     * @return bool
     * @throws \think\Exception
     */
    private function onSendSms()
    {
        $orderInfo = $this->param['order'];
        $wxappId = $orderInfo['wxapp_id'];
        return $this->sendSms('order_pay', ['order_no' => $orderInfo['order_no']], $wxappId);
    }

    /**
     * 微信订阅消息通知用户
     * @return bool|mixed
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    private function onSendWxSubMsg()
    {
        $orderInfo = $this->param['order'];
        $orderType = $this->param['order_type'];
        $wxappId = $orderInfo['wxapp_id'];

        // 获取订阅消息配置
        $template = SettingModel::getItem('submsg', $wxappId)['order']['payment'];
        if (empty($template['template_id'])) {
            return false;
        }
        // 发送订阅消息
        return $this->sendWxSubMsg($wxappId, [
            'touser' => $orderInfo['user']['open_id'],
            'template_id' => $template['template_id'],
            'page' => "{$this->pageUrl[$orderType]}?order_id={$orderInfo['order_id']}",
            'data' => [
                // 订单编号
                $template['keywords'][0] => ['value' => $orderInfo['order_no']],
                // 下单时间
                $template['keywords'][1] => ['value' => format_time($orderInfo['pay_time'])],
                // 订单金额
                $template['keywords'][2] => ['value' => $orderInfo['pay_price']],
                // 商品名称
                $template['keywords'][3] => ['value' => $this->getFormatGoodsName($orderInfo['goods'])],
            ]
        ]);
    }

    /**
     * 格式化商品名称
     * @param $goodsData
     * @return string
     */
    private function getFormatGoodsName($goodsData)
    {
        return $this->getSubstr($goodsData[0]['goods_name']);
    }
}