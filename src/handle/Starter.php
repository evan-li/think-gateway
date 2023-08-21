<?php

namespace evanlee\gateway\handle;

use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;

class Starter
{

    public static function startGateway()
    {
        // wss服务
        $context = [];
        $config = config('worker.gateway');
        // gateway 进程，这里使用Text协议，可以用telnet测试
        $gateway = new Gateway($config['socket'] ?? 'websocket://0.0.0.0:2345', $context);
        // gateway名称，status方便查看
        $gateway->name = $config['name'] ?? 'GatewayWorker';
        // gateway进程数
        $gateway->count = $config['count'] ?? 4;
        // 本机ip，分布式部署时使用内网ip
        $gateway->lanIp = $config['lanIp'] ?? '127.0.0.1';
        // 内部通讯起始端口，假如$gateway->count=4，起始端口为4000
        // 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口
        $gateway->startPort = $config['startPort'] ?? 4000;
        // 服务注册地址
        $gateway->registerAddress = config('worker.register.address', '127.0.0.1:1238');
        // 心跳间隔
        $gateway->pingInterval = $config['pingInterval'] ?? 30;
        $gateway->pingNotResponseLimit = $config['pingNotResponseLimit'] ?? 2;
        $gateway->pingData = $config['pingData'] ?? '';
        $gateway->secretKey = $config['secretKey'] ?? '';
    }

    public static function startBusinessWorker()
    {
        $config = config('worker.business');

        // businessWorker 进程
        $worker = new BusinessWorker();
        // worker名称
        $worker->name = $config['name'] ?? 'BusinessWorker';
        // businessWorker进程数量
        $worker->count = $config['4'] ?? 4;
        // 服务注册地址
        $worker->registerAddress = config('worker.register.address', '127.0.0.1:1238');
        // 命名空间
        $worker->eventHandler = $config['eventHandler'] ?? Events::class;
        $worker->secretKey = $config['secretKey'] ?? '';
        $worker->reusePort = $config['reusePort'] ?? false;
    }

    public static function startRegister()
    {
        $config = config('worker.register');
        // register 必须是text协议
        $register = new Register('text://' . ($config['address'] ?? '0.0.0.0:1238'));
        $register->name = $config['name'] ?? 'Register';
        $register->secretKey = $config['secretKey'] ?? '';
    }
}