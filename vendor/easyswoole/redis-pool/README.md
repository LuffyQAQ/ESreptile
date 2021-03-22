# Redis-Pool
Redis-Pool 基于 [pool连接池管理](https://github.com/easy-swoole/pool),[redis协程客户端](https://github.com/easy-swoole/redis) 封装的组件


## 安装
```shell
composer require easyswoole/redis-pool
```


## 连接池注册
使用连接之前注册redis连接池:

```php
//redis连接池注册(config默认为127.0.0.1,端口6379)
\EasySwoole\RedisPool\RedisPool::getInstance()->register(new \EasySwoole\Redis\Config\RedisConfig(),'redis');

//redis集群连接池注册
\EasySwoole\RedisPool\RedisPool::getInstance()->register(new \EasySwoole\Redis\Config\RedisClusterConfig([
        ['172.16.253.156', 9001],
        ['172.16.253.156', 9002],
        ['172.16.253.156', 9003],
        ['172.16.253.156', 9004],
    ]
),'redisCluster');
```

## 连接池配置
当注册好时,将返回连接池的poolConf用于配置连接池:

```php
$redisPoolConfig = \EasySwoole\RedisPool\RedisPool::getInstance()->register(new \EasySwoole\Redis\Config\RedisConfig());
//配置连接池连接数
$redisPoolConfig->setMinObjectNum(5);
$redisPoolConfig->setMaxObjectNum(20);

$redisClusterPoolConfig = \EasySwoole\RedisPool\RedisPool::getInstance()->register(new \EasySwoole\Redis\Config\RedisClusterConfig([
        ['172.16.253.156', 9001],
        ['172.16.253.156', 9002],
        ['172.16.253.156', 9003],
        ['172.16.253.156', 9004],
    ]
));
//配置连接池连接数
$redisPoolConfig->setMinObjectNum(5);
$redisPoolConfig->setMaxObjectNum(20);
```

## 使用连接池:

```php
//defer方式获取连接
$redis = \EasySwoole\RedisPool\RedisPool::defer();
$redisCluster = \EasySwoole\RedisPool\RedisPool::defer();
$redis->set('a', 1);
$redisCluster->set('a', 1);

//invoke方式获取连接
\EasySwoole\RedisPool\RedisPool::invoke(function (\EasySwoole\Redis\Redis $redis) {
    var_dump($redis->set('a', 1));
});
\EasySwoole\RedisPool\RedisPool::invoke(function (\EasySwoole\Redis\Redis $redis) {
    var_dump($redis->set('a', 1));
});

//获取连接池对象
$redisPool = \EasySwoole\RedisPool\RedisPool::getInstance()->getPool();
$redisClusterPool = \EasySwoole\RedisPool\RedisPool::getInstance()->getPool();

$redis = $redisPool->getObj();
$redisPool->recycleObj($redis);
```
！！！注意，在未指定连接池名称是，注册的连接池名称为默认的```default```
