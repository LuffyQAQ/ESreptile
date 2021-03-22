<?php


namespace App\Utility;


use EasySwoole\Component\Singleton;
use EasySwoole\RedisPool\RedisPool;

class RedisQueue
{
    use Singleton;

    public static $redisName = 'redis';

    public function push($key,$value)
    {

        return RedisPool::invoke(function (\EasySwoole\Redis\Redis $redis) use($key,$value){
            //判断是否数组

            if(is_array($value))
            {
                $value = json_encode($value,JSON_UNESCAPED_UNICODE);
            }
            return $redis->rPush($key,$value);
        },self::$redisName,0);
    }
    public function pop($key)
    {
        return RedisPool::invoke(function (\EasySwoole\Redis\Redis $redis) use($key){
            return $redis->lPop($key);
        },self::$redisName,0);
    }
    public function reset()
    {

    }
}