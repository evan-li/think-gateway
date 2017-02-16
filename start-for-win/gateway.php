#!/usr/bin/env php
<?php
define('APP_PATH', __DIR__ . '/../../application/');

define('BIND_MODULE','worker/Starter');

define('START_GATEWAY', true);

// 加载框架引导文件
require __DIR__ . '/../../thinkphp/start.php';