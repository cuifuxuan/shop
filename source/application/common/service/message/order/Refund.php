<?php

namespace app\common\service\message\order;

use app\common\service\message\Basics;
use app\common\model\Setting as SettingModel;
use app\common\enum\OrderType as OrderTypeEnum;

/**
 * 消息通知服务 [订单售后]
 * Class Refund
 * @package app\common\service\message\order
 */
class Refund extends Basics
{
    /**
     * 参数列表
     * @var array
     */
    protected $param = [
        'refund' => [],     // 退款单信息
        'order_no' => [],      // 订单信息
        'order_type' => OrderTypeEnum::MASTER,      // 订单类型
    ];

    /**
     * 订单页面链接
     * @var array
     */
    private $pageUrl = [
        OrderTypeEnum::MASTER => 'pages/order/refund/index',
        OrderTypeEnum::SHARING => 'pages/sharing/order/refund/index',
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
        // 微信订阅消息通知用户
        $this->onSendWxSubMsg();
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
        $refundInfo = $this->param['refund'];
        $orderNo = $this->param['order_no'];
        $orderType = $this->param['order_type'];
        $wxappId = $refundInfo['wxapp_id'];

        // 获取订阅消息配置
        $template = SettingModel::getItem('submsg', $wxappId)['order']['refund'];
        if (empty($template['template_id'])) {
            return false;
        }

        // 发送订阅消息
        return $this->sendWxSubMsg($wxappId, [
            'touser' => $refundInfo['user']['open_id'],
            'template_id' => $template['template_id'],
            'page' => "{$this->pageUrl[$orderType]}",
            'data' => [
                // 售后类型
                $template['keywords'][0] => ['value' => $refundInfo['type']['text']],
                // 状态
                $template['keywords'][1] => ['value' => $refundInfo['status']['text']],
                // 订单编号
                $template['keywords'][2] => ['value' => $orderNo],
                // 申请时间
                $template['keywords'][3] => ['value' => $refundInfo['create_time']],
                // 申请原因
                $template['keywords'][4] => ['value' => $this->getSubstr($refundInfo['apply_desc'])],
            ]
        ]);
    }

}