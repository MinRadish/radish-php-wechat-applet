<?php
namespace Radish\WeChatApplet\Traits;

use Radish\WeChatApplet\Exception\WeChatAppletException;
use Radish\Network\Curl;

/**
* @author Radish 1004622952@qq.com 2019-03-15
* 将微信Api接口定义无需参数的处理方法
*/
trait EasyFunction
{
    /**
     * 输出xml字符
    **/
    public function arrayToXml(array $array, $time = true)
    {
        $xml = "<xml>";
        if (!isset($array['CreateTime']) && $time) {
            $array['CreateTime'] = time();
        }
        foreach ($array as $key => $val)
        {
            if (is_numeric($val)) {
                $xml .= "<".$key.">".$val."</".$key.">";
            } else if ($key == 'KfAccount') {
                $xml .= "<TransInfo><".$key."><![CDATA[".$val."]]></".$key."></TransInfo>";
            } else {
                $xml .= "<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml .= "</xml>";

        return $xml;
    }

    /**
     * 接收消息
     * @return xml 获取微信发送的XML
     */
    public function getXml()
    {
        return file_get_contents('php://input');
    }

    /**
     * XML转换成数组
     * @param  xml $xml 
     * @return array
     */
    public function xmlToArray($xml)
    {
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * 获取微信关注者发送的信息
     * @return array 
     */
    public function getUserNews()
    {
        $xml = $this->getXml();

        return $this->xmlToArray($xml);
    }

    /**
     * 获取微信服务器IP列表
     * @return array 
     */
    public function getServerIpList()
    {
        //HTTP GET
        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=' . $this->getAccessToken();
        $result = Curl::get($url);

        return $this->getMessage($result, '获取微信服务器IP列表失败！');
    }

    /**
     * 转换发送者与接收者
     * @param  array $array 关注着发送的XMl
     * @return array 包含发送者和接收者信息
     */
    public function transformation(array $array)
    {
        return ['ToUserName' => $array['FromUserName'], 'FromUserName' => $array['ToUserName']];
    }

    /**
     * 获取请求时的错误信息
     * @param  json-string $json   微信响应数据
     * @param  string $message 信息提示
     * @return array          响应数据格式化后信息
     */
    protected function getMessage($json, $message = '未知错误！')
    {
        $array = json_decode($json, true);
        if (isset($array['errcode']) && $array['errcode'] != 0) {
            $mes = $this->getCodeMap($array['errcode']) ?: $message;
            throw new WeChatAppletException($mes, $json);
        } else {
            return $array;
        }
    }

    /**
     * 获取错误代码
     * @param  string $key 代码
     * @return String 错误代码与信息
     */
    protected function getCodeMap($key)
    {
        $codeMap = [
            //auth.code2Session
            '-1' => '系统繁忙，此时请开发者稍候再试',
            '0' => '请求成功',
            '40029' => 'code 无效',
            '45011' => '频率限制，每个用户每分钟100次',
            //订阅消息
            '40003' => 'touser字段openid为空或者不正确',
            '40037' => '订阅模板id为空不正确',
            '43101' => '用户拒绝接受消息，如果用户之前曾经订阅过，则表示用户取消了订阅关系',
            '47003' => '模板参数不准确，可能为空或者不满足规则，errmsg会提示具体是哪个字段出错',
            '41030' => 'page路径不正确，需要保证在现网版本小程序中存在，与app.json保持一致',
        ];
        $info = isset($codeMap[$key]) ? $codeMap[$key] : false;

        return $info;
    }

    /**
     * 获取API地址
     * @param  string $key 代码
     * @return string URL
     */
    protected function getApiUrl($key)
    {
        $urlMap = [
            // 登录凭证校验: GET
            'code_session' => 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code',
            // 发送订阅消息: POST
            'send_subscribe' => 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=',
        ];

        return $urlMap[$key];
    }
}