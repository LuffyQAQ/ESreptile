<?php
namespace EasySwoole\EasySwoole;


use App\Process\ConsumeProcess;
use App\Process\ProductProcess;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\Redis;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
        //注册redis连接池
        $redisData = Config::getInstance()->getConf('REDIS');
        $redisConfig = new RedisConfig($redisData);
        Redis::getInstance()->register('redis',$redisConfig);
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
        //设置爬取链接
        \App\Utility\Config::getInstance()
            ->setStartUrl('http://www.netbian.com/meinv/index_2.htm')
            ->setConsumeCoroutineNum(5)
            ->setProductCoroutineNum(5);
        //注册生产进程
        ServerManager::getInstance()
            ->getSwooleServer()
            ->addProcess((new ProductProcess())->getProcess());
        //注册消费进程
        ServerManager::getInstance()
            ->getSwooleServer()
            ->addProcess((new ConsumeProcess())->getProcess());

    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}