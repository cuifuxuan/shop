<?php

namespace app\api\controller\wxapp;

use app\api\controller\Controller;

/**
 * form_id 管理 (已废弃)
 * Class Formid
 * @package app\api\controller\wxapp
 */
class Formid extends Controller
{
    /**
     * 新增form_id
     * (因微信模板消息已下线，所以formId取消不再收集)
     * @param $formId
     * @return array
     */
    public function save($formId)
    {
        return $this->renderSuccess();
    }

}