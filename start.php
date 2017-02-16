#!/usr/bin/env php
<?php

if(strpos(strtolower(PHP_OS), 'win') === 0) {
    exit("start.php not support windows, please use start_for_win.bat\n");
}else{

    // 检查扩展
    if(!extension_loaded('pcntl')) {
        exit("Please install pcntl extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
    }

    if(!extension_loaded('posix')) {
        exit("Please install posix extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
    }

}

define('APP_PATH', __DIR__ . '/../application/');

define('BIND_MODULE','worker/Starter');

// 定义服务启动项
define('START_REGISTER', true);
define('START_GATEWAY', true);
define('START_BUSINESS', true);

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';