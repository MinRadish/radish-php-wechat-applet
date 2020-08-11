<?php
namespace Radish\WeChatApplet;

/**
* @author Radish
* 微信小程序接口公共类
*/
abstract class WeChatApplet
{
    protected static $appId = 'wxb2d69ed0b2152ef6';
    protected static $appSecret = 'c9261a72b7ce7382225abbf6c949170c';

    use Traits\EasyFunction;
    use Traits\CustomCache;
    use Traits\MessageManage;
    use Traits\AccessToken;
    use Traits\UserManage;
    use Traits\Login;
    use Traits\Subscribe;
    use Traits\Tool;

    public function __construct(array $options = [])
    {
        if (isset($options['appId'])) {
            self::$appId = $options['appId'];
        }
        if (isset($options['appSecret'])) {
            self::$appSecret = $options['appSecret'];
        }
    }
}