# think-gateway
基于tp5的gateway worker扩展

使用方式: 

1. 创建一个GatewayWorker控制器, 在application目录下, 创建一个控制器,继承`think\gateway\Server`类,  如在`application/worker/controller`目录中创建一个控制器:
  `app\worker\Starter`
  ```php
  <?php

  namespace app\worker\controller;

  use gateway\Server;

  class Starter extends Server
  {

  }
  ```

2. 创建服务入口文件, 在public目录下增加一个入口文件,用于启动GatewayWorker服务.
  如: `websocket.php` 文件: 
  ```php
  #!/usr/bin/env php
  <?php
  define('APP_PATH', __DIR__ . '/../application/');

  define('BIND_MODULE','worker/Starter');

  // 加载框架引导文件
  require __DIR__ . '/../thinkphp/start.php';
  ```
  在该文件中直接将Starter控制器绑定到文件中

3. 到public目录中执行  `php ./websocket.php` 命令即可启动服务, 默认Register/Gateway/Business都会启动

4. 如果要分布式部署,即当前服务器不是全部启动,启动时增加参数即可
  register=1 表示启动Register服务
  gateway=1 表示启动Gateway服务
  business=1 表示启动Business服务
  注意: 由于是在命令行启动, 所以参数要以url方式带入到启动命令中,并且带参数时需要增加action层路径.
  如: 
  `php ./websocket.php /index/register/1` 表示只启动Register服务
  `php ./websocket.php /index/gateway/1/business/1` 表示启动gateway服务及business服务
  
5. 在windows系统中由于php不能同时启动多个进程, 每个进程需要分开启动, 直接运行 websocket-for-win.bat 即可