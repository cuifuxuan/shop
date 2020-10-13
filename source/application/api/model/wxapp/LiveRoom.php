<?php

namespace app\api\model\wxapp;

use app\common\model\wxapp\LiveRoom as LiveRoomModel;
use app\common\enum\live\LiveStatus as LiveStatusEnum;

/**
 * 微信小程序直播间模型
 * Class LiveRoom
 * @package app\api\model\wxapp
 */
class LiveRoom extends LiveRoomModel
{
    /**
     * 隐藏的字段
     * @var array
     */
    protected $hidden = [
        'is_delete',
        'wxapp_id',
        'create_time',
        'update_time',
    ];

    /**
     * 获取直播间列表
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        // 直播间列表
        // mix: 可设置live_status条件来显示不同直播状态的房间
        $this->where('live_status', '<>', 107); // 已过期的不显示
        $list = $this->where('is_delete', '=', 0)
            ->order([
                'is_top' => 'desc',
                'live_status' => 'asc',
                'create_time' => 'desc'
            ])->paginate(15, false, [
                'query' => \request()->request()
            ]);
        // 整理api数据
        foreach ($list as &$item) {
            $item['live_status_text_1'] = LiveStatusEnum::data()[$item['live_status']]['name'];
            $item['live_status_text_2'] = $item['live_status_text_1'];
            $item['live_status'] == 101 && $item['live_status_text_1'] = '正在直播中';
            $item['live_status'] == 102 && $item['live_status_text_1'] = $this->semanticStartTime($item->getData('start_time')) . ' 开播';
        }
        return $list;
    }

    /**
     * 语义化开播时间
     * @param $startTime
     * @return string
     */
    private function semanticStartTime($startTime)
    {
        // 转换为 YYYYMMDD 格式
        $startDate = date('Ymd', $startTime);
        // 获取今天的 YYYY-MM-DD 格式
        $todyDate = date('Ymd');
        // 获取明天的 YYYY-MM-DD 格式
        $tomorrowDate = date('Ymd', strtotime('+1 day'));
        // 使用IF当作字符串判断是否相等
        if ($startDate == $todyDate) {
            return date('今天H:i', $startTime);
        } elseif ($startDate == $tomorrowDate) {
            return date('明天H:i', $startTime);
        }
        // 常规日期格式
        return date('m/d H:i', $startTime);
    }

}