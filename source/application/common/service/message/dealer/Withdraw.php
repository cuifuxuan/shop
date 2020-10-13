<?php

namespace app\common\service\message\dealer;

use app\common\service\message\Basics;
use app\common\model\Setting as SettingModel;
use app\common\enum\dealer\withdraw\ApplyStatus as ApplyStatusEnum;

/**
 * 消息通知服务 [分销商提现]
 * Class Withdraw
 * @package app\common\service\message\dealer
 */
class Withdraw extends Basics
{
    /**
     * 参数列表
     * @var array
     */
    protected $param = [
        'withdraw' => [],   // 提现记录
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
        $withdrawInfo = $this->param['withdraw'];
        $userInfo = $this->param['user'];
        $wxappId = $withdrawInfo['wxapp_id'];

        // 根据提现状态获取对应的消息模板
        $template = $this->getTemplateByStatus($withdrawInfo);
        if ($template === false) {
            return false;
        }

        // 发送订阅消息
        return $this->sendWxSubMsg($wxappId, [
            'touser' => $userInfo['open_id'],
            'template_id' => $template['template_id'],
            'page' => 'pages/dealer/index/index',
            'data' => $this->getTemplateData($withdrawInfo, $template)
        ]);
    }

    /**
     * 生成消息内容
     * @param $withdrawInfo
     * @param $template
     * @return array
     */
    private function getTemplateData($withdrawInfo, $template)
    {
        if ($withdrawInfo['apply_status'] == ApplyStatusEnum::AUDIT_PASS) {
            return [
                // 提现金额
                $template['keywords'][0] => ['value' => $withdrawInfo['money']],
                // 打款方式
                $template['keywords'][1] => ['value' => $withdrawInfo['pay_type']['text']],
                // 打款原因
                $template['keywords'][2] => ['value' => '分销商提现'],
            ];
        }
        if ($withdrawInfo['apply_status'] == ApplyStatusEnum::AUDIT_REJECT) {
            return [
                // 提现金额
                $template['keywords'][0] => ['value' => $withdrawInfo['money']],
                // 申请时间
                $template['keywords'][1] => ['value' => $withdrawInfo['create_time']],
                // 原因
                $template['keywords'][2] => ['value' => $this->getSubstr($withdrawInfo['reject_reason'])],
            ];
        }
        return [];
    }

    /**
     * 根据提现状态获取对应的消息模板
     * @param $withdrawInfo
     * @return bool
     */
    private function getTemplateByStatus($withdrawInfo)
    {
        $wxappId = $withdrawInfo['wxapp_id'];
        // 获取订阅消息配置
        $templateGroup = SettingModel::getItem('submsg', $wxappId)['dealer'];
        if (
            $withdrawInfo['apply_status'] == ApplyStatusEnum::AUDIT_PASS
            && !empty($templateGroup['withdraw_01']['template_id'])
        ) {
            return $templateGroup['withdraw_01'];
        }
        if (
            $withdrawInfo['apply_status'] == ApplyStatusEnum::AUDIT_REJECT
            && !empty($templateGroup['withdraw_02']['template_id'])
        ) {
            return $templateGroup['withdraw_02'];
        }
        return false;
    }

}
