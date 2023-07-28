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

## 验证Token并获取解析内容

~~~php
use think\facade\JwtClient;
// 获取验证结果
$validateResult = JwtClient::validate($token);
// 验证成功
if(!is_null($validateResult[0])){
    // 打印令牌数据
    print_r($validateResult[0]);
}
// 验证失败
else{
    // 输出错误信息
    echo $validateResult[1]->getMessage();
}
~~~

## 解析Token

~~~php
use think\facade\JwtClient;
// 获取解析结果
$parseResult = JwtClient::parse($token);
// 验证成功
if(!is_null($parseResult[0])){
    // 打印令牌数据
    print_r($parseResult[0]);
}
// 解析失败
else{
    // 输出错误信息
    echo $parseResult[1]->getMessage();
}
~~~