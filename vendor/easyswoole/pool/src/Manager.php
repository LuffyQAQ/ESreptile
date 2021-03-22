<?php


namespace EasySwoole\Pool;


use EasySwoole\Component\Singleton;

class Manager
{
    use Singleton;

    protected $container = [];

    function register(AbstractPool $pool,string $name = null):Manager
    {
        if($name === null){
            $name = get_class($pool);
        }
        $this->container[$name] = $pool;
        return $this;
    }

    function get(string $name):?AbstractPool
    {
        if(isset($this->container[$name])){
            return $this->container[$name];
        }
        return null;
    }

}