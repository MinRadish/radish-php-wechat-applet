<?php
/**
 * 小程序登录等操作
 * @authors Radish (1004622952@qq.com)
 * @date    2020-05-27 10:41 Wednesday
 */

namespace Radish\WeChatApplet\Traits;

use Radish\Network\Curl;
use Radish\WeChatApplet\Exception\WeChatAppletException;

trait Login
{
    public function getSessionKey($code)
    {
        $url = sprintf($this->getApiUrl('code_session'), self::$appId, self::$appSecret, $code);
        $result = Curl::get($url);

        return $this->getMessage($result, '获取session_key失败！');
    }

    /**
     * 解密数据
     * @param  string $sessionKey
     * @param  string $encryptedData
     * @param  string $iv
     * @return String|bool 解密结果
     */
    public function decryptWechatData($sessionKey, $encryptedData, $iv)
    {
        if (strlen($sessionKey) != 24) {
            throw new WeChatAppletException('encodingAesKey 非法', $sessionKey);
        }
        $aesKey = base64_decode($sessionKey);
        if (strlen($iv) != 24) {
            throw new WeChatAppletException('iv 非法', $iv);
        }
        $aesIv = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIv);
        $dataObj = json_decode($result);
        if($dataObj == NULL) {
            throw new WeChatAppletException('aes 解密失败', $result);
        } else if ($dataObj->watermark->appid != self::$appId) {
            throw new WeChatAppletException('aes 解密失败-appid不一致', $result);
        } else {
            return $result;
        }
    }   
}