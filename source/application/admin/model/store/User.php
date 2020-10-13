<?php

namespace app\admin\model\store;

use app\common\exception\BaseException;
use app\common\model\store\User as StoreUserModel;

/**
 * 商家用户模型
 * Class StoreUser
 * @package app\admin\model
 */
class User extends StoreUserModel
{
    /**
     * 新增商家用户记录
     * @param int $wxappId
     * @param array $data
     * @return bool|false|int
     */
    public function add($wxappId, $data)
    {
        return $this->save([
            'user_name' => $data['user_name'],
            'password' => yoshop_hash($data['password']),
            'is_super' => 1,
            'wxapp_id' => $wxappId,
        ]);
    }

    /**
     * 商家用户登录
     * @param int $wxappId
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function login($wxappId)
    {
        // 获取获取商城超级管理员用户信息
        $user = $this->getSuperStoreUser($wxappId);
        if (empty($user)) {
            throw new BaseException(['msg' => '超级管理员用户信息不存在']);
        }
        $this->loginState($user);
    }

    /**
     * 获取获取商城超级管理员用户信息
     * @param $wxappId
     * @return User|null
     * @throws \think\exception\DbException
     */
    private function getSuperStoreUser($wxappId)
    {
        return static::detail(['wxapp_id' => $wxappId, 'is_super' => 1], ['wxapp']);
    }

    /**
     * 删除小程序下的商家用户
     * @param $wxappId
     * @return false|int
     */
    public function setDelete($wxappId)
    {
        return $this->save(['is_delete' => 1], ['wxapp_id' => $wxappId]);
    }

}
