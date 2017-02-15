# think-gateway
基于tp5的gateway worker扩展

### 安装:

1. 创建thinkphp5项目

   ```sh
   composer create-project topthink/think gateway
   ```


2. 添加think-gateway依赖

   ```sh
   composer require evan-li/think-gateway
   ```

   ​

### 简单使用: 

1. 创建一个`Starter`控制器，继承`think\gateway\Server`类,用来启动Worker

   `application/worker/controller/Starter.php`

   ```php
   <?php
   namespace app\worker\controller;

   use think\gateway\Server;

   class Starter extends Server
   {

   }
   ```

2. 在`public`目录下添加入口文件: `websocket.php`

   文件内容: 

   ```php
    #!/usr/bin/env php
     <?php
     define('APP_PATH', __DIR__ . '/../application/');

     define('BIND_MODULE','worker/Starter');

     // 加载框架引导文件
     require __DIR__ . '/../thinkphp/start.php';
   ```

3. 运行服务

   1. 在linux中, 打开控制台, 执行命令: 

      ```sh
      php ./websocket.php
      ```

   2. windows系统中, 首先要切换依赖包:

      ```
      // 移除linux版的gateway-worker依赖
      composer remove workerman/gateway-worker
      // 添加windows版gateway-worker依赖
      composer require workerman/gateway-worker-for-win
      ```

      然后, 将项目中的 `websocket-for-win.bat`文件移动到public目录下, 直接双击执行

      *到此为止, 我们的gateway-worker服务就跑起来啦*


1. 分布式部署

   如果需要分布式部署,我们可以通过url参数的形式限制启动什么服务

   如: 

   - 只启动Register服务时, 运行: `php ./websocket.php /index/register/1`
   - 启动gateway及business服务时, 运行: `php ./websocket.php /index/gateway/1/business/1`

   > 参数说明: 
   >
   > register=1 表示启动Register服务
   >
   > gateway=1 表示启动Gateway服务
   >
   > business=1 表示启动Business服务

   ​

   > 注意: 由于是在命令行启动, 所以参数要以url方式带入到启动命令中,并且带参数时需要增加action操作路径.
   >
   > 如上面的例子中, 在 `php ./websocket.php`后面加的参数是 `/index/register/1`, 这里的第一层`index`是action操作(即控制器的方法)







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

`think\gateway\Events`类简单封装了一个连接的初始化事件响应,以及心跳信息忽略, 建议自定义的Events类直接继承 `think\gateway\Events`类并实现具体的 `onMessage`方法即可, 另外, 实现的onMessage方法中, 要记得对心跳信息进行处理

