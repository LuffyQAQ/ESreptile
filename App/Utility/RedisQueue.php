<?php


namespace App\Utility;


use EasySwoole\Component\Singleton;
use EasySwoole\RedisPool\Redis;

class RedisQueue
{
    use Singleton;

    public static $redisName = 'redis';

    public function push($key,$value)
    {
        return Redis::invoke(self::$redisName,function (\EasySwoole\Redis\Redis $redis) use($key,$value){
            //判断是否数组
            if(is_array($value))
            {
                $value = json_encode($value,JSON_UNESCAPED_UNICODE);
            }
            return $redis->rPush($key,$value);
        },0);
    }
    public function pop($key)
    {
        return Redis::invoke(self::$redisName,function (\EasySwoole\Redis\Redis $redis) use($key){
            return $redis->lPop($key);
        },0);
    }
    public function reset()
    {

    }
}