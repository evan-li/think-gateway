[TOC]

> 说明:
> 
> thinkphp5.0扩展: v0分支
> 
> thinkphp5.1扩展: v1分支

# think-gateway扩展

基于tp5.1的gateway worker扩展


## 结构说明:

~~~
vendor (composer第三方库目录)
├─src                         核心代码目录
│  ├─command                  命令行目录
│  │  ├─Worker.php            worker启动命令(windows不支持, 需要分开启动register/gateway/business)
│  │  ├─WorkerBusiness.php    BusinessWorker进程启动命令 
│  │  ├─WorkerGateway.php     GatewayWorker进程启动命令 
│  │  └─WorkerRegister.php    Register进程启动命令 
│  │
│  │─handle                   
│  │  ├─Events.php            默认的消息事件处理类
│  │  └─Starter.php           启动辅助类
│  │
│  │─common.php               命令注册文件
│  └─config.php               配置文件
│
├─composer.json               composer 定义文件
├─LICENSE                     授权说明文件
└─README.md                   README 文件
~~~



## 使用介绍

### 安装方式
```shell
composer require evan-li/think-gateway:1.*
```

### 启动服务: 
```shell
# linux 下
php think worker
# windows 下需要分别启动register/gateway/business进程
php think worker:register
## 打开新窗口
php think worker:gateway
## 打开新窗口
php think worker:business
```



### 配置说明

将src/config.php复制到配置文件目录, 并命名为`worker.php`文件

> Windows中, 由于线程操作支持的问题, 所有的count *(子worker启动的线程数)* 配置都不会生效



### 分布式部署

如果需要分布式部署,我们可以通过`worker:register`, `worker:gateway`, `worker:business`在不同的服务器分别启动服务即可
```shell    
# 启动命令说明
php think worker             #启动全部服务(window下无效)
php think worker:register    #注册服务
php think worker:gateway     #gateway(网关)服务
php think worker:business    #业务服务
```



##  类说明


### Events类介绍

`think\gateway\Events`类简单封装了一个连接的初始化事件响应,以及心跳信息忽略, 建议自定义的Events类直接继承 `think\gateway\Events`类并实现具体的 `processMessage`方法即可

#### $INIT_EVENT_KEY 属性

```
客户端连接后服务端给客户端发送初始化事件数据的操作key值
```

#### $INIT_EVENT_VALUE 属性

```
客户端连接后服务端首次给客户端发送初始化事件数据的操作名
```

当客户端连接服务端后, 服务端会直接给客户端发送一个初始化事件, 将client_id返回
如`$initEventKey`设置为 `action`, `$initEventValue` 设置为 `init`,
则初始化后服务端给客户端发送一次格式为:` {action: 'init', client_id: xxxxxx } `的消息
客户端可以通过此事件获取client_id并到业务系统中将client_id注册到当前用户中