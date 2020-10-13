<?php

namespace app\api\model\bsy;

use app\common\model\bsy\BsyWeight as BsyWeightModel;


/**
 * 碧生源体重信息
 * Class BsyWeight
 * @package app\api\model\bsy
 */
class BsyWeight extends BsyWeightModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'user_id',
        'bsy_user_id',
        'add_time',
        'wxapp_id'
    ];

    /**
     * 获取最新体重记录
     * @param $user_id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public function getweightinfo($user_id)
    {
        //$couponList = $this->order(['sort' => 'asc', 'create_time' => 'desc'])->limit($limit)->select();
        return $this->where(['user_id'=>$user_id])->order(['id'=>'desc'])->find();
    }


    /**
     * 设置错误信息
     * @param $error
     */
    protected function setError($error)
    {
        empty($this->error) && $this->error = $error;
    }

    /**
     * 是否存在错误
     * @return bool
     */
    public function hasError()
    {
        return !empty($this->error);
    }

}
