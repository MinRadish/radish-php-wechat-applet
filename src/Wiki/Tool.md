# 小程序工具特性说明

## 生成小程序带参数二维码

**示例代码**

~~~
public function getQrCode()
{
    if (!$this->qr_code) {
        $applet = new WeChatApplet;
        $params = [
            'scene' => $this->id,
            // 'page' => '', //必须是已经发布的小程序存在的页面（否则报错），例如 pages/index/index, 根路径前不要填加 /,不能携带参数（参数请放在scene字段里），如果不填写这个字段，默认跳主页面
            // 'width' => '',
            // 'auto_color' => '',
            // 'line_color' => '',
            // 'is_hyaline' => '',
        ];
        $result = $applet->unlimited($params);
        $this->qr_code = $this->saveImage($result);
        if ($this->save()) {
            return $this->qr_code;
        } else {
            throw new \Exception('保存失败！', -1);
        }
    } else {
        return $this->qr_code;
    }
}

public function saveImage($content)
{
    $dirPath = config('uploads_root') . config('activity_qrcode');
    $savename = date('Ymd');
    if (!is_dir($dirPath . $savename)) {
        mkdir($dirPath . $savename, 0777, true);
    }
    $savename .= DIRECTORY_SEPARATOR . $this->id . '.png';
    $bool = file_put_contents($dirPath . $savename, $content);
    if ($bool) {
        $path = config('activity_qrcode') . $savename;
        return $path;
    } else {
        throw new \Exception('保存失败！', -1);
    }
}
~~~

## 验证词汇敏感性

**示例代码**

~~~
$applet = new \common\WeChat\WeChatApplet;
$params = [
    'content' => $msg,
];
return $applet->msgSecCheck($params);
~~~

## 验证图片敏感性

**示例代码**

~~~
global $_applet;
if (!$_applet) {
    $applet = new \common\WeChat\WeChatApplet;
}
$params = [
    'media' => $media,
];
return $applet->imgSecCheck($params);
~~~