<?php

namespace EasySwoole\RedisPool;

use EasySwoole\Component\Singleton;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\Pool\Config as PoolConfig;
use EasySwoole\Redis\Redis as RedisClient;
use EasySwoole\Redis\RedisCluster;
use EasySwoole\RedisPool\Exception\Exception;

class RedisPool
{
    use Singleton;

    protected $container = [];

    function register(RedisConfig $config, string $name ='default', ?string $cask = null): PoolConfig
    {
        if(isset($this->container[$name])){
            //已经注册，则抛出异常
            throw new RedisPoolException("redis pool:{$name} is already been register");
        }
        if($cask){
            $ref = new \ReflectionClass($cask);
            if((!$ref->isSubclassOf(RedisClient::class)) && (!$ref->isSubclassOf(RedisCluster::class))){
                throw new Exception("cask {$cask} not a sub class of EasySwoole\Redis\Redis or EasySwoole\Redis\RedisCluster");
            }
        }
        $pool = new Pool($config,$cask);
        $this->container[$name] = $pool;
        return $pool->getConfig();
    }

    function getPool(string $name ='default'): ?Pool
    {
        if (isset($this->container[$name])) {
            return $this->container[$name];
        }
        return null;
    }

    static function defer(string $name ='default',$timeout = null):?RedisClient
    {
        $pool = static::getInstance()->getPool($name);
        if($pool){
            return $pool->defer($timeout);
        }else{
            return null;
        }
    }

    static function invoke(callable $call,string $name ='default',float $timeout = null)
    {
        $pool = static::getInstance()->getPool($name);
        if($pool){
            return $pool->invoke($call,$timeout);
        }else{
            return null;
        }
    }
}