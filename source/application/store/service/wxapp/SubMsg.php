<?php

namespace app\store\service\wxapp;

use app\store\model\Wxapp as WxappModel;
use app\store\model\Setting as SettingModel;
use app\common\library\helper;
use app\common\service\Basics;
use app\common\library\wechat\WxSubMsg;
use app\common\exception\BaseException;

/**
 * 小程序订阅消息服务类
 * Class SubMsg
 * @package app\store\service\wxapp
 */
class SubMsg extends Basics
{
    /* @var $WxSubMsg WxSubMsg 小程序订阅消息api类 */
    private $WxSubMsg;

    /**
     * 构造方法
     * SubMsg constructor.
     * @param null $wxappId
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function __construct($wxappId = null)
    {
        // 小程序订阅消息api类
        $wxConfig = WxappModel::getWxappCache($wxappId);
        $this->WxSubMsg = new WxSubMsg($wxConfig['app_id'], $wxConfig['app_secret']);
    }

    /**
     * 一键添加订阅消息
     * @return bool
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    public function shuttle()
    {
        // 拉取我的模板列表
        $myList = $this->getMyTemplateList();
        // 筛选出未添加的模板
        $addedList = $this->getNotAddedTemplates($myList);
        // 批量添加订阅消息模板
        $newList = $this->onBatchAdd($addedList);
        // 全部模板列表
        $tplList = array_merge($newList, $myList);
        // 保存全部模板id
        return $this->saveAll($tplList);

    }

    /**
     * 保存全部模板id
     * @param $tplList
     * @return bool
     * @throws \think\exception\DbException
     */
    private function saveAll($tplList)
    {
        // 整理模板id
        $data = SettingModel::getItem('submsg');
        foreach ($data as &$group) {
            foreach ($group as &$item) {
                if (!isset($item['title'])) continue;
                $tpl = helper::arraySearch($tplList, 'title', $item['title']);
                $tpl != false && $item['template_id'] = $tpl['priTmplId'];
            }
        }
        // 保存数据
        return (new SettingModel)->edit('submsg', $data);
    }

    /**
     * 批量添加订阅消息模板
     * [并且记录返回的priTmplId]
     * @param $newList
     * @return array
     * @throws BaseException
     */
    private function onBatchAdd($newList)
    {
        foreach ($newList as &$item) {
            // 请求微信api, 添加模板记录
            $response = $this->WxSubMsg->addTemplate($item['tid'], $item['kidList'], $item['sceneDesc']);
            if ($response === false) {
                throw new BaseException(['msg' => "添加模板[{$item['sceneDesc']}]失败：" . $this->WxSubMsg->getError()]);
            }
            // 记录template_id
            $item['priTmplId'] = $response['priTmplId'];
        }
        return $newList;
    }

    /**
     * 筛选出未添加的模板
     * @param $myList
     * @return array
     */
    private function getNotAddedTemplates($myList)
    {
        // 所有订阅消息模板列表
        $templateLists = $this->getTemplates();
        if (empty($myList)) return $templateLists;
        // 整理未添加的
        $data = [];
        foreach ($templateLists as $item) {
            if (helper::arraySearch($myList, 'title', $item['title']) === false) {
                $data[] = $item;
            }
        }
        return $data;
    }

    /**
     * 所有订阅消息模板列表
     * @return array
     */
    private function getTemplates()
    {
        return [
            // 支付成功通知
            [
                'tid' => 9344,
                'title' => '新订单提醒',
                'kidList' => [1, 2, 4, 3],
                'sceneDesc' => '新订单提醒',
            ],
            // 订单发货通知
            [
                'tid' => 855,
                'title' => '订单发货通知',
                'kidList' => [1, 2, 12, 11, 17],
                'sceneDesc' => '订单发货通知',
            ],
            // 售后状态通知
            [
                'tid' => 5049,
                'title' => '售后状态通知',
                'kidList' => [1, 6, 2, 3, 4],
                'sceneDesc' => '售后状态通知',
            ],
            // 拼团进度通知
            [
                'tid' => 5008,
                'title' => '拼团进度通知',
                'kidList' => [1, 5, 7, 3, 6],
                'sceneDesc' => '拼团进度通知',
            ],
            // 分销商入驻审核通知
            [
                'tid' => 4050,
                'title' => '代理商入驻审核通知',
                'kidList' => [1, 2, 3, 4],
                'sceneDesc' => '分销商入驻审核通知',
            ],
            // 提现成功通知
            [
                'tid' => 2001,
                'title' => '提现成功通知',
                'kidList' => [1, 3, 4],
                'sceneDesc' => '提现成功通知',
            ],
            // 提现失败通知
            [
                'tid' => 3173,
                'title' => '提现失败通知',
                'kidList' => [1, 3, 4],
                'sceneDesc' => '提现失败通知',
            ],

        ];
    }

    /**
     * 拉取我的模板列表
     * @return mixed
     * @throws BaseException
     */
    private function getMyTemplateList()
    {
        $response = $this->WxSubMsg->getTemplateList();
        if ($response === false) {
            throw new BaseException(['msg' => '拉取模板列表失败：' . $this->WxSubMsg->getError()]);
        }
        return $response['data'];
    }

}