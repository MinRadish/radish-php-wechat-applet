<?php
/**
 * 工具特性
 * @authors Radish (minradish@163.com)
 * @date    2020-08-07 19:01 Friday
 */

namespace Radish\WeChatApplet\Traits;

use Radish\Network\Curl;
use Radish\WeChatApplet\Exception\WeChatAppletException;

trait Tool
{
    /**
     * 存在敏感词汇时的错误diamante
     * @var integer
     */
    protected static $msgErrorCode = 87014;

    /**
     * 生成不限制的小程序带参数二维码
     * @param  array  $param  图片参数
     * @return array          微信响应数据
     */
    public function unlimited(array $param)
    {
        $options = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ];
        $param = json_encode($param, JSON_UNESCAPED_UNICODE);
        $result = Curl::post($this->getToolApiUrl('unlimited'), $param, $options);

        return $this->getToolMessage($result, '生成图片信息失败！');
    }

    /**
     * 检测敏感词汇
     * @param  array  $param 请求数据
     * @return bool          通过为true,不通过为false
     */
    public function msgSecCheck(array $param)
    {
        $options = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ];
        $param = json_encode($param, JSON_UNESCAPED_UNICODE);
        $result = Curl::post($this->getToolApiUrl('msg_sec_check'), $param, $options);
        $array = json_decode($result, true);
        if (isset($array['errcode']) && $array['errcode'] != 0) {
            if ($array['errcode'] == self::$msgErrorCode) {
                return false;
            } else {
                $mes = $this->getCodeMap($array['errcode']) ?: '验证敏感词失败！';
                throw new WeChatAppletException($mes, $json);
            }
        } else {
            return true;
        }
    }

    /**
     * 校验一张图片是否含有违法违规内容。
     * @param  array  $param 请求数据
     * @return bool          通过为true,不通过为false
     */
    public function imgSecCheck($param)
    {
        $options = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ];
        // $param = json_encode($param, JSON_UNESCAPED_UNICODE);
        $result = Curl::post($this->getToolApiUrl('img_sec_check'), $param, $options);
        $array = json_decode($result, true);
        if (isset($array['errcode']) && $array['errcode'] != 0) {
            if ($array['errcode'] == self::$msgErrorCode) {
                return false;
            } else {
                $mes = $this->getCodeMap($array['errcode']) ?: '验证敏感词失败！';
                throw new WeChatAppletException($mes, $json);
            }
        } else {
            return true;
        }
    }

    /**
     * 请求地址
     * @param   string $key key
     * @return  string      URL
     */
    protected function getToolApiUrl($key)
    {
        $urlMap = [
            // http请求方式: POST
            'unlimited' => 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=',
            // http请求方式: POST
            'msg_sec_check' => 'https://api.weixin.qq.com/wxa/msg_sec_check?access_token=',
            // http请求方式: POST
            'img_sec_check' => 'https://api.weixin.qq.com/wxa/img_sec_check?access_token=',
        ];

        return $urlMap[$key] . $this->getAccessToken();
    }

    protected function getToolMessage($result, $message = '未知错误！')
    {
        $bool = json_decode($result, true);
        if ($bool) {
            return $this->getMessage($result, '生成图片信息失败！');
        } else {
            return $result;
        }
    }
}