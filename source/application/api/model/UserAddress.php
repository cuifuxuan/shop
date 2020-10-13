<?php

namespace app\api\model;

use app\common\model\Region;
use app\common\model\UserAddress as UserAddressModel;

/**
 * 用户收货地址模型
 * Class UserAddress
 * @package app\common\model
 */
class UserAddress extends UserAddressModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'wxapp_id',
        'create_time',
        'update_time'
    ];

    /**
     * @param $user_id
     * @return false|static[]
     * @throws \think\exception\DbException
     */
    public function getList($user_id)
    {
        return self::all(compact('user_id'));
    }

    /**
     * 新增收货地址
     * @param User $user
     * @param $data
     * @return mixed
     */
    public function add($user, $data)
    {
        return $this->transaction(function () use ($user, $data) {
            // 整理地区信息
            $region = explode(',', $data['region']);
            $provinceId = Region::getIdByName($region[0], 1);
            $cityId = Region::getIdByName($region[1], 2, $provinceId);
            $regionId = Region::getIdByName($region[2], 3, $cityId);
            // 验证城市ID是否合法
            if (!$this->checkCityId($cityId)) return false;
            // 添加收货地址
            $this->allowField(true)->save([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'province_id' => $provinceId,
                'city_id' => $cityId,
                'region_id' => $regionId,
                'detail' => $data['detail'],
                'district' => ($regionId === 0 && !empty($region[2])) ? $region[2] : '',
                'user_id' => $user['user_id'],
                'wxapp_id' => self::$wxapp_id
            ]);
            // 设为默认收货地址
            !$user['address_id'] && $user->save(['address_id' => $this['address_id']]);
            return true;
        });
    }

    /**
     * 编辑收货地址
     * @param $data
     * @return false|int
     */
    public function edit($data)
    {
        // 整理地区信息
        $region = explode(',', $data['region']);
        $provinceId = Region::getIdByName($region[0], 1);
        $cityId = Region::getIdByName($region[1], 2, $provinceId);
        $regionId = Region::getIdByName($region[2], 3, $cityId);
        // 验证城市ID是否合法
        if (!$this->checkCityId($cityId)) return false;
        // 更新收货地址
        return $this->allowField(true)->save([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'province_id' => $provinceId,
                'city_id' => $cityId,
                'region_id' => $regionId,
                'detail' => $data['detail'],
                'district' => ($regionId === 0 && !empty($region[2])) ? $region[2] : '',
            ]) !== false;
    }

    /**
     * 验证城市ID是否合法
     * @param $cityId
     * @return bool
     */
    private function checkCityId($cityId)
    {
        if ($cityId <= 0) {
            \log_write([
                'system_msg' => '选择的城市不存在',
                'param' => \request()->param()
            ], 'error');
            $this->error = '很抱歉，您选择的城市不存在';
            return false;
        }
        return true;
    }

    /**
     * 设为默认收货地址
     * @param User $user
     * @return int
     */
    public function setDefault($user)
    {
        // 设为默认地址
        return $user->save(['address_id' => $this['address_id']]);
    }

    /**
     * 删除收货地址
     * @param User $user
     * @return int
     */
    public function remove($user)
    {
        // 查询当前是否为默认地址
        $user['address_id'] == $this['address_id'] && $user->save(['address_id' => 0]);
        return $this->delete();
    }

    /**
     * 收货地址详情
     * @param $user_id
     * @param $address_id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public static function detail($user_id, $address_id)
    {
        return self::get(compact('user_id', 'address_id'));
    }

}
