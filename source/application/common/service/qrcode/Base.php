<?php

namespace app\common\service\qrcode;

use app\common\library\wechat\Qrcode;
use app\common\model\Wxapp as WxappModel;

/**
 * 二维码服务基类
 * Class Base
 * @package app\common\service\qrcode
 */
class Base
{
    /**
     * 构造方法
     * Base constructor.
     */
    public function __construct()
    {
    }

    /**
     * 保存小程序码到文件
     * @param $wxappId
     * @param $scene
     * @param null $page
     * @return string
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    protected function saveQrcode($wxappId, $scene, $page = null)
    {
        // 文件目录
        $dirPath = RUNTIME_PATH . 'image' . '/' . $wxappId;
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        // 文件名称
        $fileName = 'qrcode_' . md5($wxappId . $scene . $page) . '.png';
        // 文件路径
        $savePath = "{$dirPath}/{$fileName}";
        if (file_exists($savePath)) return $savePath;
        // 小程序配置信息
        $wxConfig = WxappModel::getWxappCache($wxappId);
        // 请求api获取小程序码
        $Qrcode = new Qrcode($wxConfig['app_id'], $wxConfig['app_secret']);
        $content = $Qrcode->getQrcode($scene, $page);
        // 保存到文件
        file_put_contents($savePath, $content);
        return $savePath;
    }

    /**
     * 获取网络图片到临时目录
     * @param $wxappId
     * @param $url
     * @param string $mark
     * @return string
     */
    protected function saveTempImage($wxappId, $url, $mark = 'temp')
    {
        $dirPath = RUNTIME_PATH . 'image' . '/' . $wxappId;
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        $savePath = $dirPath . '/' . $mark . '_' . md5($url) . '.png';
        if (file_exists($savePath)) return $savePath;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $img = curl_exec($ch);
        curl_close($ch);
        $fp = fopen($savePath, 'w');
        fwrite($fp, $img);
        fclose($fp);
        return $savePath;
    }

}
