<?php

namespace app\common\model;

/**
 * 配送模板区域及运费模型
 * Class DeliveryRule
 * @package app\store\model
 */
class DeliveryRule extends BaseModel
{
    protected $name = 'delivery_rule';
    protected $updateTime = false;

    /**
     * 追加字段
     * @var array
     */
    protected $append = ['region_data'];

    /**
     * 地区集转为数组格式
     * @param $value
     * @param $data
     * @return array
     */
    public function getRegionDataAttr($value, $data)
    {
        return explode(',', $data['region']);
    }

}
