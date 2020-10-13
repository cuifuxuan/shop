<?php

namespace app\store\model\wxapp;

use app\common\exception\BaseException;
use app\store\model\Wxapp as WxappModel;
use app\common\model\wxapp\LiveRoom as LiveRoomModel;
use app\common\library\wechat\live\Room as LiveRoomApi;

/**
 * 微信小程序直播间模型
 * Class LiveRoom
 * @package app\store\model\wxapp
 */
class LiveRoom extends LiveRoomModel
{
    /**
     * 获取直播间列表
     * @param string $search 检索词
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($search = '')
    {
        !empty($search) && $this->where('room_name|anchor_name', 'like', "%{$search}%");
        return $this->where('is_delete', '=', 0)
            ->order([
                'is_top' => 'desc',
                'live_status' => 'asc',
                'create_time' => 'desc'
            ])
            ->paginate(15, false, [
                'query' => \request()->request()
            ]);
    }

    /**
     * 设置直播间置顶状态
     * @param $isTop
     * @return false|int
     */
    public function setIsTop($isTop)
    {
        return $this->save(['is_top' => (int)$isTop]);
    }

    /**
     * 刷新直播间列表(同步微信api)
     * 每次拉取上限100条数据
     * @throws BaseException
     * @throws \think\exception\DbException
     * @throws \Exception
     */
    public function refreshLiveList()
    {
        // 获取微信api最新直播间列表信息
        $originRoomList = $this->getOriginRoomList();
        // 获取微信直播间的房间id集
        $originRoomIds = $this->getOriginRoomIds($originRoomList);
        // 已存储的所有房间id集
        $localRoomIds = $this->getLocalRoomIds();
        // 同步新增直播间
        $this->refreshLiveNew($localRoomIds, $originRoomIds, $originRoomList);
        // 同步删除直播间
        $this->refreshLiveRemove($localRoomIds, $originRoomIds);
        // 同步更新直播间
        $this->refreshLiveUpdate($localRoomIds, $originRoomIds, $originRoomList);
        return true;
    }

    /**
     * 获取微信api最新直播间列表信息
     * @return array
     * @throws BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    private function getOriginRoomList()
    {
        // 小程序配置信息
        $wxConfig = WxappModel::getWxappCache();
        // 请求api数据
        $LiveRoomApi = new LiveRoomApi($wxConfig['app_id'], $wxConfig['app_secret']);
        $response = $LiveRoomApi->getLiveRoomList();
        if ($response === false) {
            throw new BaseException(['msg' => '直播房间列表api请求失败：' . $LiveRoomApi->getError()]);
        }
        // 格式化返回的列表数据
        $originRoomList = [];
        foreach ($response['room_info'] as $item) {
            $originRoomList[$item['roomid']] = $item;
        }
        return $originRoomList;
    }

    /**
     * 获取微信直播间的房间id集
     * @param $originRoomList
     * @return array
     */
    private function getOriginRoomIds($originRoomList)
    {
        $originRoomIds = [];
        foreach ($originRoomList as $item) {
            $originRoomIds[] = $item['roomid'];
        }
        return $originRoomIds;
    }

    /**
     * 获取数据库中已存在的roomid
     * @return array
     */
    private function getLocalRoomIds()
    {
        return $this->where('is_delete', '=', 0)->column('room_id', 'id');
    }

    /**
     * 同步新增直播间
     * @param array $localRoomIds 本地直播间id集
     * @param array $originRoomIds 最新直播间id集
     * @param array $originRoomList 最新直播间列表
     * @return array|bool|false
     * @throws \Exception
     */
    private function refreshLiveNew($localRoomIds, $originRoomIds, $originRoomList)
    {
        // 需要新增的直播间ID
        $newLiveRoomIds = array_values(array_diff($originRoomIds, $localRoomIds));
        if (empty($newLiveRoomIds)) return true;
        // 整理新增数据
        $saveData = [];
        foreach ($newLiveRoomIds as $roomId) {
            $item = $originRoomList[$roomId];
            $saveData[] = [
                'room_id' => $roomId,
                'room_name' => $item['name'],
                'cover_img' => $item['cover_img'],
                'share_img' => $item['share_img'],
                'anchor_name' => $item['anchor_name'],
                'start_time' => $item['start_time'],
                'end_time' => $item['end_time'],
                'live_status' => $item['live_status'],
                'wxapp_id' => self::$wxapp_id,
            ];
        }
        // 批量新增直播间
        return $this->isUpdate(false)->saveAll($saveData, false);
    }

    /**
     * 同步更新直播间
     * @param array $localRoomIds 本地直播间id集
     * @param array $originRoomIds 最新直播间id集
     * @param array $originRoomList 最新直播间列表
     * @return array|bool|false
     * @throws \Exception
     */
    private function refreshLiveUpdate($localRoomIds, $originRoomIds, $originRoomList)
    {
        // 需要新增的直播间ID
        $updatedLiveRoomIds = array_values(array_intersect($originRoomIds, $localRoomIds));
        if (empty($updatedLiveRoomIds)) return true;
        // 根据直播间id获取主键id
        $idArr = $this->getLocalIdsByRoomIds($localRoomIds);
        // 整理新增数据
        $saveData = [];
        foreach ($updatedLiveRoomIds as $roomId) {
            $item = $originRoomList[$roomId];
            $saveData[] = [
                'id' => $idArr[$roomId],
                'room_id' => $roomId,
                'room_name' => $item['name'],
                'cover_img' => $item['cover_img'],
                'share_img' => $item['share_img'],
                'anchor_name' => $item['anchor_name'],
                'start_time' => $item['start_time'],
                'end_time' => $item['end_time'],
                'live_status' => $item['live_status'],
                'wxapp_id' => self::$wxapp_id,
            ];
        }
        // 批量新增直播间
        return $this->isUpdate(true)->saveAll($saveData);
    }

    /**
     * 同步删除直播间
     * @param array $localRoomIds 本地直播间id集
     * @param array $originRoomIds 最新直播间id集
     * @return array|bool|false
     * @throws \Exception
     */
    private function refreshLiveRemove($localRoomIds, $originRoomIds)
    {
        // 需要删除的直播间ID
        $removedLiveRoomIds = array_values(array_diff($localRoomIds, $originRoomIds));
        if (empty($removedLiveRoomIds)) return true;
        // 根据直播间id获取主键id
        $removedIds = $this->getLocalIdsByRoomIds($localRoomIds, $removedLiveRoomIds);
        // 批量删除直播间
        return self::destroy(array_values($removedIds));
    }

    /**
     * 根据直播间id获取主键id
     * @param array $localRoomIds
     * @param array $searchRoomIds
     * @return array
     */
    private function getLocalIdsByRoomIds($localRoomIds, $searchRoomIds = [])
    {
        $data = [];
        foreach ($localRoomIds as $id => $roomId) {
            if (empty($searchRoomIds) || in_array($roomId, $searchRoomIds)) {
                $data[$roomId] = $id;
            }
        }
        return $data;
    }

}