# think-gateway
基于tp5的gateway worker扩展

### 目录结构说明: 

~~~
vendor (composer第三方库目录)
├─src                   核心代码目录
│  ├─Server.php         GatewayWorker扩展控制器文件
│  └─Events.php         默认的消息事件处理类
│
├─start-for-win         windows版本启动文件目录
│  ├─register.php       注册服务启动文件
│  ├─gateway.php        gateway(网关)服务启动文件
│  └─business.php       业务服务启动文件
│
├─composer.json         composer 定义文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
├─start.php             linux系统服务启动文件
└─start-for-win.bat     windows系统服务启动批处理文件
~~~



### 安装:

1. 创建thinkphp5项目

   ```sh
   composer create-project topthink/think gateway
   ```


2. 添加think-gateway依赖

   ```sh
   composer require evan-li/think-gateway
   ```

   >  windows版本请使用`evan-li/think-gateway-for-win`包安装 : 
   >
   >  ```
   >  composer require evan-li/think-gateway-for-win
   >  ```

> 如果没有使用过composer, 请先看 [composer入门](http://docs.phpcomposer.com/00-intro.html) , 可以使用[composer中国镜像](https://pkg.phpcomposer.com/)
>

### 简单使用: 

1.  创建一个`Starter`控制器，继承`think\gateway\Server`类,用来启动Worker

    `application/worker/controller/Starter.php`

    ```php
    <?php
    namespace app\worker\controller;

    use think\gateway\Server;

    class Starter extends Server
    {

    }
    ```

2.  复制启动文件

     将的`start.php`文件拷贝到项目中的`public`目录中

    > 如果是windows系统, 将 `start-for-win.bat` 以及 `start-for-win`目录拷贝到 `public` 目录中

3.  运行服务

    1. 在linux中, 打开控制台, 执行命令: 

       ```sh
       php ./start.php
       ```

    2. windows系统中, 执行 `start-for-win.bat` 批处理文件即可


    *到此为止, 我们的gateway-worker服务就跑起来啦*

4.  分布式部署

    如果需要分布式部署,我们可以通过修改start.php中的配置信息来控制启动哪个服务
    ```php    
     // 定义服务启动项
     define('START_REGISTER', true);
     define('START_GATEWAY', true);
     define('START_BUSINESS', true);
    ```
     以上三项分别控制Register、Gateway、Business的启动服务, 不需要启动哪个服务直接去掉或注释就可以了

    如:  只启动Register服务时, 将 `START_GATEWAY` 与 `START_BUSINESS` 项常量注释

    > windows 系统中, 直接使用`php`命令运行 `start-for-win` 目录中对应服务的文件


### 消息处理

处理客户端发送的消息时, 增加一个`MessageHandler`类, 继承`think\gateway\Events`类, 并实现`processMessage`方法即可, 需要注意的是, `processMessage`方法是静态方法

增加`MessageHandler`类之后需要在`Starter`控制器中设置消息处理Handler:

`app\worker\controller\Starter 类:`

```php
class Starter extends Server
{

    protected $businessEventHandler = 'app\worker\util\EventsHandler';

}
```

`app\worker\util\EventsHanler 类:`

```php
<?php
namespace app\worker\util;

use think\gateway\Events;

class EventsHandler extends Events
{

    public static function processMessage($client_id, $message)
    {
        parent::processMessage($client_id, $message); // do some thing
    }
}
```

> 默认不设置消息处理类的时候, 调用的是Events类



###  项目搭建完成后的目录结构 *(只做参考用)*:

> 使用正常的tp5目录结构, 框架本身相关的文件及目录不再展示

```
www  WEB部署目录（或者子目录）
├─application                  应用目录
│  ├─worker                    worker模块目录
│  │  ├─controller             控制器目录
│  │  │  └─Starter.php         gateway启动控制器文件
│  │  └─util                   工具类目录
│  │     └─EventsHandler.php   business线程事件处理工具类
│  └─ ...                      其他框架相关文件及目录
│
├─public                       WEB目录（对外访问目录）
│  ├─start-for-win             windows版本启动文件目录
│  │  ├─register.php           注册服务启动文件
│  │  ├─gateway.php            gateway(网关)服务启动文件
│  │  └─business.php           业务服务启动文件
│  ├─start.php                 linux系统启动文件
│  ├─start-for-win.bat         windows系统服务启动批处理文件
│  └─...                       其他框架相关文件及目录
│
├─vendor                       第三方类库目录（Composer依赖库）
│  ├─evan-li                   
│  │  └─think-gateway          think-gateway类库目录
│  └─...                       其他第三方类库目录
└─...                          其他框架相关文件及目录
```






##  类说明 

### Server类介绍

Server类是基于GatewayWorker的控制器扩展类, 使用自己的控制器继承Server类即可, 继承后可以通过属性重写的方式覆盖父类的相关属性, Server类中的属性主要分为4类:

1. 注册服务相关属性
2. gateway服务相关属性
3. business服务相关属性
4. 心跳相关属性

```php

    // --------------------  注册服务  --------------------
	// 注册服务地址
    protected $registerAddress = '127.0.0.1:1238';
	// 注册服务线程名称，status方便查看
    protected $registerName = 'RegisterServer';

    // -------------------  gateway服务  -------------------
	// gateway监听地址，用于客户端连接
    protected $gatewaySocketUrl = 'websocket://0.0.0.0:8282';
    // 网关服务线程名称，status方便查看
    protected $gatewayName = 'GatewayServer';
    // gateway进程数
    protected $gatewayCount = 1;
    // 本机ip，分布式部署时使用内网ip，用于与business内部通讯
    protected $gatewayLanIp = '127.0.0.1';
    // 内部通讯起始端口，每个 gateway 实例应该都不同，假如$gateway->count=4，起始端口为4000
    // 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口
    protected $gatewayLanStartPort = 2900;
    // gateway服务秘钥
    protected $gatewaySecretKey = '';

    // -------------------- business服务  -------------------
    // business服务名称，status方便查看
    protected $businessName = 'BusinessServer';
    // business进程数
    protected $businessCount = 4;
    // 业务服务事件处理
    protected $businessEventHandler = 'gateway\Events';
    // 业务超时时间，可用来定位程序卡在哪里
    protected $businessProcessTimeout = 30;
    // 业务超时后的回调，可用来记录日志
    protected $businessProcessTimeoutHandler = '\\Workerman\\Worker::log';
    // 业务服务秘钥
    protected $businessSecretKey = '';


    // -------------------- 心跳相关  ------------------------
    // 心跳时间间隔，设为0则表示不检测心跳
    protected $pingInterval = 25;
	// $gatewayPingNotResponseLimit * $gatewayPingInterval 时间内，客户端未发送任何数据，断开客户端连接
	// 设为0表示不监听客户端返回数据
    protected $pingNotResponseLimit = 2;
    // 服务端向客户端发送的心跳数据，为空不给客户端发送心跳数据
    // 定义为静态属性方便外部调用
    protected $pingData = '2';
```





### Events类介绍

`think\gateway\Events`类简单封装了一个连接的初始化事件响应,以及心跳信息忽略, 建议自定义的Events类直接继承 `think\gateway\Events`类并实现具体的 `processMessage`方法即可

