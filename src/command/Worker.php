<?php

namespace evanlee\gateway\command;

use evanlee\gateway\handle\Starter;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use Workerman\Worker as WorkermanWorker;

class Worker extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('worker');
        // 设置参数
        
    }

    protected function execute(Input $input, Output $output)
    {
        if (strpos(strtolower(PHP_OS), 'win') === 0) {
            exit("windows 不支持此调用方式, 请分别使用命令: php think worker:register, php think worker:gateway, php think worker:business 开启服务 \n");
        }
        Starter::startRegister();
        Starter::startGateway();
        Starter::startBusinessWorker();
        WorkermanWorker::runAll();
    }

}
