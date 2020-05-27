# php对微信小程序的API的调用

- *需自定义一个类并继承 Radish\WeChat\WeChatApplet 自定义CaChe抽象方法*

**public function cacheGet($key = 'access_token', $default = false);**

**public function cacheSet($key, $val, $timeout = 7140);**
