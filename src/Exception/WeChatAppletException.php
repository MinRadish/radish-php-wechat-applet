<?php
namespace Radish\WeChatApplet\Exception;

/**
* @author Radish 1004622952@qq.com 2019-03-15
* 微信API错误异常类
*/

class WeChatAppletException extends \Exception
{
    protected $message;
    protected $result;

    public function __construct($message, $result)
    {
        $this->message = $message;
        $this->result = $result;
        $this->createLog();
    }

    protected function createLog()
    {
        if (PHP_SAPI == 'cli') {
            $path = $_SERVER['PWD'];
            if (PHP_OS == 'WINNT') {
                preg_match('/^\/(\w+\/?)/', $path, $array);
                if (count($array) >= 2) {
                    $dir = trim($array[1], '\/') . ':';
                    $path = $dir . substr($path, strlen($array[0]) - 1);
                }
            }
        } else {
            $path = $_SERVER['DOCUMENT_ROOT'];
        }
        if (is_dir($path)) {
            $file = $path . '/WeChatApplet.log';
            $time = date('Y-m-d H:i:s');
            file_put_contents($file, $time . PHP_EOL . $this->result() . PHP_EOL . 'message:' . $this->message . PHP_EOL, FILE_APPEND);
        }
    }

    public function message()
    {
        return $this->message;
    }

    public function result()
    {
        return $this->result;
    }
}