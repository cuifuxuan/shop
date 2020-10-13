<?php

namespace app\common\library\printer\engine;

use app\common\library\helper;

/**
 * 365云打印引擎
 * Class PrintCenter
 * @package app\common\library\printer\engine
 */
class PrintCenter extends Basics
{
    /** @const API地址 */
    const API = 'http://open.printcenter.cn:8080/addOrder';

    /**
     * 执行订单打印
     * @param $content
     * @return bool|mixed
     */
    public function printTicket($content)
    {
        // 构建请求参数
        $context = stream_context_create([
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded ",
                'method' => 'POST',
                'content' => http_build_query([
                    'deviceNo' => $this->config['deviceNo'],
                    'key' => $this->config['key'],
                    'printContent' => $content,
                    'times' => $this->times
                ]),
            ]
        ]);
        // API请求：开始打印
        $result = file_get_contents(self::API, false, $context);
        // 处理返回结果
        $result = helper::jsonDecode($result);
        // 记录日志
        log_write([
            'describe' => 'PrintCenter(365) PrintTicket',
            'result' => $result
        ]);
        // 返回状态
        if ($result['responseCode'] != 0) {
            $this->error = $result['msg'];
            return false;
        }
        return true;
    }

}
