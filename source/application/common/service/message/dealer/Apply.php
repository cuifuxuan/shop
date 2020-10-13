<?php

namespace app\common\service\message\dealer;

use app\common\service\message\Basics;
use app\common\model\Setting as SettingModel;
use app\common\enum\dealer\ApplyStatus as ApplyStatusEnum;

/**
 * 消息通知服务 [分销商入驻]
 * Class Apply
 * @package app\common\service\message\dealer
 */
class Apply extends Basics
{
    /**
     * 参数列表
     * @var array
     */
    protected $param = [
        'apply' => [],   // 申请记录
        'user' => [],    // 用户信息
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
        $applyInfo = $this->param['apply'];
        $userInfo = $this->param['user'];
        $wxappId = $applyInfo['wxapp_id'];

        // 获取订阅消息配置
        $template = SettingModel::getItem('submsg', $wxappId)['dealer']['apply'];
        if (empty($template['template_id'])) {
            return false;
        }

        // 发送订阅消息
        return $this->sendWxSubMsg($wxappId, [
            'touser' => $userInfo['open_id'],
            'template_id' => $template['template_id'],
            'page' => 'pages/dealer/index/index',
            'data' => [
                // 申请时间
                $template['keywords'][0] => ['value' => $applyInfo['apply_time']],
                // 审核状态
                $template['keywords'][1] => ['value' => ApplyStatusEnum::data()[$applyInfo['apply_status']]['name']],
                // 审核时间
                $template['keywords'][2] => ['value' => $applyInfo['audit_time']],
                // 备注信息
                $template['keywords'][3] => ['value' => $this->getRemarkValue($applyInfo)],
            ]
        ]);
    }

    /**
     * 备注信息
     * @param $applyInfo
     * @return string
     */
    private function getRemarkValue($applyInfo)
    {
        $remark = '分销商入驻审核通知';
        if ($applyInfo['apply_status'] == 30) {
            $remark .= "\n驳回原因：{$applyInfo['reject_reason']}";
        }
        return $this->getSubstr($remark);
    }

}