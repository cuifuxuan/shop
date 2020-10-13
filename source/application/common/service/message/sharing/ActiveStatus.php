<?php

namespace app\common\service\message\sharing;

use app\common\service\message\Basics;
use app\common\model\Setting as SettingModel;
use app\common\enum\sharing\ActiveStatus as ActiveStatusEnum;

/**
 * 消息通知服务 [拼团进度]
 * Class ActiveStatus
 * @package app\common\service\message\sharing
 */
class ActiveStatus extends Basics
{
    /**
     * 参数列表
     * @var array
     */
    protected $param = [
        'active' => [],         // 拼单详情
        'status' => false,      // 拼单状态
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
     * @return bool
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    private function onSendWxSubMsg()
    {
        // 拼单详情
        $activeInfo = $this->param['active'];
        $status = $this->param['status'];
        $wxappId = $activeInfo['wxapp_id'];

        // 获取订阅消息配置
        $template = SettingModel::getItem('submsg', $wxappId)['sharing']['active_status'];
        if (empty($template['template_id'])) {
            return false;
        }
        // 发送订阅消息
        foreach ($activeInfo['users'] as $item) {
            $this->sendWxSubMsg($wxappId, [
                'touser' => $item['user']['open_id'],
                'template_id' => $template['template_id'],
                'page' => "pages/sharing/active/index?active_id={$activeInfo['active_id']}",
                'data' => [
                    // 拼团商品
                    $template['keywords'][0] => ['value' => str_substr($activeInfo['goods']['goods_name'], 20)],
                    // 拼团价格
                    $template['keywords'][1] => ['value' => $item['sharing_order']['pay_price']],
                    // 成团人数
                    $template['keywords'][2] => ['value' => $activeInfo['people']],
                    // 拼团进度
                    $template['keywords'][3] => ['value' => "已有{$activeInfo['actual_people']}人参与"],
                    // 温馨提示
                    $template['keywords'][4] => ['value' => ActiveStatusEnum::data()[$status]['name']],
                ]
            ]);
        }
        return true;
    }


}