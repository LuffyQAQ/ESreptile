<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/10/15 0015
 * Time: 14:46
 */

namespace  EasySwoole\RedisPool;

use EasySwoole\Pool\MagicPool;
use EasySwoole\Redis\Config\RedisClusterConfig;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\Redis\Redis;
use EasySwoole\Redis\RedisCluster;

class Pool extends MagicPool
{
    function __construct(RedisConfig $redisConfig,?string $cask = null)
    {
        parent::__construct(function ()use($redisConfig,$cask){
            if($cask){
                return new $cask($redisConfig);
            }
            if ($redisConfig instanceof RedisClusterConfig){
                $redis = new RedisCluster($redisConfig);
            }else{
                $redis = new Redis($redisConfig);
                $redis->connect($redisConfig->getTimeout());
            }
            return $redis;
        },new PoolConfig());
    }


    /**
     * @param RedisPool $redis
     * @return bool
     */
    public function itemIntervalCheck($redis): bool
    {
        /*
         * 如果最后一次使用时间超过autoPing间隔
         */
        if($this->getConfig()->getAutoPing() > 0 && (time() - $redis->__lastUseTime > $this->getConfig()->getAutoPing())){
            try{
                //执行一个ping
                $redis->ping();
                //标记使用时间，避免被再次gc
                $redis->__lastUseTime = time();
                return true;
            }catch (\Throwable $throwable){
                //异常说明该链接出错了，return 进行回收
                return false;
            }
        }else{
            return true;
        }
    }
}