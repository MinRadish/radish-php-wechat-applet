<?php
namespace Radish\WeChatApplet;

/**
* @author Radish
* 微信小程序接口公共类
*/
abstract class WeChatApplet
{
    protected static $AppID = 'wxb2d69ed0b2152ef6';
    protected static $AppSecret = 'c9261a72b7ce7382225abbf6c949170c';

    use Traits\EasyFunction;
    use Traits\CustomerService;
    use Traits\CustomerServiceSession;
    use Traits\MessageManage;
    use Traits\AccessToken;
    use Traits\Material;
    use Traits\WebAuth;
    use Traits\UserManage;

    public function __construct(array $options = [])
    {
        if (isset($options['appId'])) {
            self::$AppID = $options['appId'];
        }
        if (isset($options['appSecret'])) {
            self::$AppSecret = $options['appSecret'];
        }
    }
}