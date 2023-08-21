<?php

namespace evanlee\gateway\command;

use evanlee\gateway\handle\Starter;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use Workerman\Worker;

class WorkerBusiness extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('worker:business');
        // 设置参数

    }

    protected function execute(Input $input, Output $output)
    {
        Starter::startBusinessWorker();
        Worker::runAll();
    }

}
