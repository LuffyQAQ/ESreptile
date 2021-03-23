<?php


namespace EasySwoole\EasySwoole;


use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\RedisPool;
use App\Process\ConsumeProcess;
use App\Process\ProductProcess;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
        //注册连接池
        $redisData = Config::getInstance()->getConf('REDIS');
        $redisConfig = new RedisConfig($redisData);
        RedisPool::getInstance()->register($redisConfig,'redis');

        //onRequest
        Di::getInstance()->set(SysConst::HTTP_GLOBAL_ON_REQUEST,function (Request $request,Response $response){
            return true;
        });

    }

    public static function mainServerCreate(EventRegister $register)
    {
        //设置爬取链接 // 可修改图片分类meinv，keai，qiche等等分类
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
}
