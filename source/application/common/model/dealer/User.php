<?php

namespace app\common\model\dealer;

use app\common\model\BaseModel;

/**
 * 分销商用户模型
 * Class Apply
 * @package app\common\model\dealer
 */
class User extends BaseModel
{
    protected $name = 'dealer_user';

    /**
     * 强制类型转换
     * @var array
     */
    protected $type = [
        'first_num' => 'integer',
        'second_num' => 'integer',
        'third_num' => 'integer',
    ];

    /**
     * 关联会员记录表
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('app\common\model\User');
    }

    /**
     * 关联推荐人表
     * @return \think\model\relation\BelongsTo
     */
    public function referee()
    {
        return $this->belongsTo('app\common\model\User', 'referee_id')
            ->field(['user_id', 'nickName']);
    }

    /**
     * 获取分销商用户信息
     * @param $userId
     * @param array $with
     * @return static|null
     * @throws \think\exception\DbException
     */
    public static function detail($userId, $with = ['user', 'referee'])
    {
        return self::get($userId, $with);
    }

    /**
     * 是否为分销商
     * @param $userId
     * @return bool
     */
    public static function isDealerUser($userId)
    {
        return !!(new static)->where('user_id', '=', $userId)
            ->where('is_delete', '=', 0)
            ->value('user_id');
    }

    /**
     * 新增分销商用户记录
     * @param $user_id
     * @param $data
     * @return false|int
     * @throws \think\exception\DbException
     */
    public static function add($user_id, $data)
    {
        $model = static::detail($user_id) ?: new static;
        return $model->save(array_merge([
            'user_id' => $user_id,
            'is_delete' => 0,
            'wxapp_id' => $model::$wxapp_id
        ], $data));
    }

    /**
     * 发放分销商佣金
     * @param $user_id
     * @param $money
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public static function grantMoney($user_id, $money)
    {
        // 分销商详情
        $model = static::detail($user_id);
        if (!$model || $model['is_delete']) {
            return false;
        }
        // 累积分销商可提现佣金
        $model->setInc('money', $money);
        // 记录分销商资金明细
        Capital::add([
            'user_id' => $user_id,
            'flow_type' => 10,
            'money' => $money,
            'describe' => '订单佣金结算',
            'wxapp_id' => $model['wxapp_id'],
        ]);
        return true;
    }

}