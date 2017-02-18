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

// 标记是全局启动
define('GLOBAL_START', 1);

require_once __DIR__ . '/../vendor/autoload.php';

use Workerman\Worker;

// 加载所有/start/start_*.php，以便启动所有服务
foreach(glob(__DIR__ . '/starter/start_*.php') as $start_file)
{
    require_once $start_file;
}
// 运行所有服务
Worker::runAll();
