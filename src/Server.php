<?php
namespace think\gateway;

use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Workerman\Worker;

/**
 * Class Server
 * GatewayWorker控制器扩展
 */
class Server
{

    // --------------------  注册服务  --------------------
    /**
     * 注册服务地址
     * @var string
     */
    protected $registerAddress = '127.0.0.1:1238';
    /**
     * 注册服务线程名称，status方便查看
     * @var string
     */
    protected $registerName = 'RegisterServer';



    // -------------------  gateway服务  -------------------
    /**
     * gateway监听地址，用于客户端连接
     * @var string
     */
    protected $gatewaySocketUrl = 'websocket://0.0.0.0:8282';
    /**
     * 网关服务线程名称，status方便查看
     * @var string
     */
    protected $gatewayName = 'GatewayServer';
    /**
     * gateway进程数
     * @var int
     */
    protected $gatewayCount = 1;
    /**
     * 本机ip，分布式部署时使用内网ip，用于与business内部通讯
     * @var string
     */
    protected $gatewayLanIp = '127.0.0.1';
    /**
     * 内部通讯起始端口，每个 gateway 实例应该都不同，假如$gateway->count=4，起始端口为4000
     * 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口
     * @var int
     */
    protected $gatewayLanStartPort = 2900;
    /**
     * gateway服务秘钥
     * @var string
     */
    protected $gatewaySecretKey = '';


    // -------------------- business服务  -------------------
    /**
     * business服务名称，status方便查看
     * @var string
     */
    protected $businessName = 'BusinessServer';
    /**
     * business进程数
     * @var int
     */
    protected $businessCount = 4;
    /**
     * 业务服务事件处理
     * @var string
     */
    protected $businessEventHandler = 'think\gateway\Events';
    /**
     * 业务超时时间，可用来定位程序卡在哪里
     * @var int
     */
    protected $businessProcessTimeout = 30;
    /**
     * 业务超时后的回调，可用来记录日志
     * @var callable
     */
    protected $businessProcessTimeoutHandler = '\\Workerman\\Worker::log';
    /**
     * 业务服务秘钥
     * @var string
     */
    protected $businessSecretKey = '';



    // -------------------- 心跳服务  ------------------------
    /**
     * 心跳时间间隔，设为0则表示不检测心跳
     * @var int
     */
    protected $pingInterval = 25;
    /**
     * $gatewayPingNotResponseLimit * $gatewayPingInterval 时间内，客户端未发送任何数据，断开客户端连接
     * 设为0表示不监听客户端返回数据
     * @var int
     */
    protected $pingNotResponseLimit = 2;
    /**
     * 服务端向客户端发送的心跳数据，为空不给客户端发送心跳数据
     * 定义为静态属性方便外部调用
     * @var string
     */
    protected $pingData = '2';



    /**
     * 构造方法, 分别初始化Register,Gateway及Business  并根据需求初始化
     * Server constructor.
     */
    public function __construct()
    {


        // 初始化Register线程
        if(START_REGISTER){
            $register = new Register("text://$this->registerAddress");
            $register->name = $this->registerName;
        }

        // 初始化Gateway线程
        if(START_GATEWAY){
            $gateway = new Gateway($this->gatewaySocketUrl);
            $gateway->name = $this->gatewayName;
            $gateway->count = $this->gatewayCount;
            $gateway->lanIp = $this->gatewayLanIp;
            $gateway->lanPort = $this->gatewayLanStartPort;
            $gateway->secretKey = $this->gatewaySecretKey;
            $gateway->pingInterval = $this->pingInterval;
            $gateway->pingNotResponseLimit = $this->pingNotResponseLimit;
            $gateway->pingData = $this->pingData;

            $gateway->registerAddress = $this->registerAddress;
        }

        // 初始化Business线程
        if(START_BUSINESS){
            $business = new BusinessWorker();
            $business->name = $this->businessName;
            $business->count = $this->businessCount;
            $business->eventHandler = $this->businessEventHandler;
            $business->processTimeout = $this->businessProcessTimeout;
            $business->processTimeoutHandler = $this->businessProcessTimeoutHandler;
            $business->secretKey = $this->businessSecretKey;

            $business->registerAddress = $this->registerAddress;
        }

        // 启动初始化过的进程
        Worker::runAll();

    }
}
