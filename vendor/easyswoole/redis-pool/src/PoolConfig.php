<?php


namespace EasySwoole\RedisPool;


use EasySwoole\Pool\Config;

class PoolConfig extends Config
{
    protected $autoPing=5;

    /**
     * @return mixed
     */
    public function getAutoPing()
    {
        return $this->autoPing;
    }

    /**
     * @param mixed $autoPing
     */
    public function setAutoPing($autoPing): void
    {
        $this->autoPing = $autoPing;
    }

}