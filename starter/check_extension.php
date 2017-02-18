<?php

if(strpos(strtolower(PHP_OS), 'win') !== 0) {
    // 检查扩展
    if(!extension_loaded('pcntl')) {
        exit("Please install pcntl extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
    }

    if(!extension_loaded('posix')) {
        exit("Please install posix extension. See http://doc3.workerman.net/appendices/install-extension.html\n");
    }
}
