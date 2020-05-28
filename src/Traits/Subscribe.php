<?php
/**
 * 订阅消息发送
 * @authors Radish (1004622952@qq.com)
 * @date    2020-05-28 15:04 Thursday
 */

namespace Radish\WeChatApplet\Traits;

use Radish\Network\Curl;

trait Subscribe
{
    /**
     * 发送消息
     * @param  array  $param       以类型为KEY的数组
     * @return array             微信响应数据
     */
    public function sendSubscribe(array $param)
    {
        $options = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ];
        $param = json_encode($param, JSON_UNESCAPED_UNICODE);
        $result = Curl::post($this->getApiUrl('send_subscribe') . $this->getAccessToken(), $param, $options);
        
        return $this->getMessage($result, '发送消息失败！');
    }
}