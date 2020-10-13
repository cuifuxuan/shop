<?php

namespace app\admin\model\store;

use app\common\model\store\Access as AccessModel;

/**
 * 商家用户权限模型
 * Class Access
 * @package app\admin\model\store
 */
class Access extends AccessModel
{
    /**
     * 获取权限列表
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        $all = static::getAll();
        return $this->formatTreeData($all);
    }

    /**
     * 新增记录
     * @param $data
     * @return false|int
     */
    public function add($data)
    {
        $data['wxapp_id'] = self::$wxapp_id;
        return $this->allowField(true)->save($data);
    }

    /**
     * 更新记录
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit($data)
    {
        // 判断上级角色是否为当前子级
        if ($data['parent_id'] > 0) {
            // 获取所有上级id集
            $parentIds = $this->getTopAccessIds($data['parent_id']);
            if (in_array($this['access_id'], $parentIds)) {
                $this->error = '上级权限不允许设置为当前子权限';
                return false;
            }
        }
        return $this->allowField(true)->save($data) !== false;
    }

    /**
     * 删除权限
     * @return bool|int
     * @throws \think\exception\DbException
     */
    public function remove()
    {
        // 判断是否存在下级权限
        if (self::detail(['parent_id' => $this['access_id']])) {
            $this->error = '当前权限下存在子权限，请先删除';
            return false;
        }
        return $this->delete();
    }

    /**
     * 获取所有上级id集
     * @param $access_id
     * @param null $all
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getTopAccessIds($access_id, &$all = null)
    {
        static $ids = [];
        is_null($all) && $all = $this->getAll();
        foreach ($all as $item) {
            if ($item['access_id'] == $access_id && $item['parent_id'] > 0) {
                $ids[] = $item['parent_id'];
                $this->getTopAccessIds($item['parent_id'], $all);
            }
        }
        return $ids;
    }

    /**
     * 获取权限列表
     * @param $all
     * @param int $parent_id
     * @param int $deep
     * @return array
     */
    private function formatTreeData(&$all, $parent_id = 0, $deep = 1)
    {
        static $tempTreeArr = [];
        foreach ($all as $key => $val) {
            if ($val['parent_id'] == $parent_id) {
                // 记录深度
                $val['deep'] = $deep;
                // 根据角色深度处理名称前缀
                $val['name_h1'] = $this->htmlPrefix($deep) . $val['name'];
                $tempTreeArr[] = $val;
                $this->formatTreeData($all, $val['access_id'], $deep + 1);
            }
        }
        return $tempTreeArr;
    }

    private function htmlPrefix($deep)
    {
        // 根据角色深度处理名称前缀
        $prefix = '';
        if ($deep > 1) {
            for ($i = 1; $i <= $deep - 1; $i++) {
                $prefix .= '&nbsp;&nbsp;&nbsp;├ ';
            }
            $prefix .= '&nbsp;';
        }
        return $prefix;
    }

}