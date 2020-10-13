<?php

namespace app\store\model;

use app\store\model\Goods as GoodsModel;
use app\common\model\Delivery as DeliveryModel;

/**
 * 配送模板模型
 * Class Delivery
 * @package app\common\model
 */
class Delivery extends DeliveryModel
{
    /**
     * 添加新记录
     * @param $data
     * @return bool|int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function add($data)
    {
        // 表单验证
        if (!$this->onValidate($data)) return false;
        // 保存数据
        $data['wxapp_id'] = self::$wxapp_id;
        if ($this->allowField(true)->save($data)) {
            return $this->createDeliveryRule($data['rule']);
        }
        return false;
    }

    /**
     * 编辑记录
     * @param $data
     * @return bool|int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function edit($data)
    {
        // 表单验证
        if (!$this->onValidate($data)) return false;
        // 保存数据
        if ($this->allowField(true)->save($data)) {
            return $this->createDeliveryRule($data['rule']);
        }
        return false;
    }

    /**
     * 表单验证
     * @param $data
     * @return bool
     */
    private function onValidate($data)
    {
        if (!isset($data['rule']) || empty($data['rule'])) {
            $this->error = '请选择可配送区域';
            return false;
        }
        foreach ($data['rule']['first'] as $value) {
            if ((int)$value <= 0 || (int)$value != $value) {
                $this->error = '首件或首重必须是正整数';
                return false;
            }
        }
        foreach ($data['rule']['additional'] as $value) {
            if ((int)$value <= 0 || (int)$value != $value) {
                $this->error = '续件或续重必须是正整数';
                return false;
            }
        }
        return true;
    }

    /**
     * 获取配送区域及运费设置项
     * @return array
     */
    public function getFormList()
    {
        // 所有地区
        $regions = Region::getCacheAll();
        $list = [];
        foreach ($this['rule'] as $rule) {
            $citys = explode(',', $rule['region']);
            $province = [];
            foreach ($citys as $cityId) {
                if (!isset($regions[$cityId])) continue;
                !in_array($regions[$cityId]['pid'], $province) && $province[] = $regions[$cityId]['pid'];
            }
            $list[] = [
                'first' => $rule['first'],
                'first_fee' => $rule['first_fee'],
                'additional' => $rule['additional'],
                'additional_fee' => $rule['additional_fee'],
                'province' => $province,
                'citys' => $citys,
            ];
        }
        return $list;
    }

    /**
     * 添加模板区域及运费
     * @param $data
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    private function createDeliveryRule($data)
    {
        $save = [];
        $connt = count($data['region']);
        for ($i = 0; $i < $connt; $i++) {
            $save[] = [
                'region' => $data['region'][$i],
                'first' => $data['first'][$i],
                'first_fee' => $data['first_fee'][$i],
                'additional' => $data['additional'][$i],
                'additional_fee' => $data['additional_fee'][$i],
                'wxapp_id' => self::$wxapp_id
            ];
        }
        $this->rule()->delete();
        return $this->rule()->saveAll($save);
    }

    /**
     * 删除记录
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function remove()
    {
        // 验证运费模板是否被商品使用
        if (!$this->checkIsUseGoods($this['delivery_id'])) {
            return false;
        }
        // 删除运费模板
        $this->rule()->delete();
        return $this->delete();
    }

    /**
     * 验证运费模板是否被商品使用
     * @param int $deliveryId
     * @return bool
     * @throws \think\Exception
     */
    private function checkIsUseGoods($deliveryId)
    {
        // 判断是否存在商品
        $goodsCount = (new GoodsModel)->where('delivery_id', '=', $deliveryId)
            ->where('is_delete', '=', 0)
            ->count();
        if ($goodsCount > 0) {
            $this->error = '该模板被' . $goodsCount . '个商品使用，不允许删除';
            return false;
        }
        return true;
    }

}
