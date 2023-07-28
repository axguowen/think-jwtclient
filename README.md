# ThinkPHP JsonWebToken 扩展

一个简单的 ThinkPHP JsonWebToken 扩展


## 安装
~~~
composer require axguowen/think-jwtclient
~~~

## 配置

首先配置config目录下的jwtclient.php配置文件。

## 生成Token

~~~php
use think\facade\JwtClient;
// 快速生成Token
$token = JwtClient::issue([
    'user_id' => 'jwtclient',
    'user_name' => 'myUserName',
]);

// 切换平台配置生成Token
$token = \think\facade\JwtClient::platform('other')->issue([
    'user_id' => 'jwtclient',
    'user_name' => 'myUserName',
]);

// 动态切换平台并传入自定义配置
$jwtClient = \think\facade\JwtClient::platform('admin', [
    // 颁发者
    'issuer' => 'https://www.example.com',
    // 识别ID
    'id' => 'jwtclient_2fxz',
])->issue([
    'user_id' => 'jwtclient',
    'user_name' => 'myUserName',
]);
// 返回token
echo $token;
~~~

## 解析并验证Token

~~~php
use think\facade\JwtClient;
// 获取解析结果
$parse = JwtClient::parse($token);
// 解析成功
if(!is_null($parse[0])){
    // 打印解析的数据
    print_r($parse[0]);
}
// 解析失败
else{
    // 输出错误信息
    echo $parse[1]->getMessage();
}
~~~