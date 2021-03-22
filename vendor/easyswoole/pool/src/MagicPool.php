<?php


namespace EasySwoole\Pool;


class MagicPool extends AbstractPool
{
    protected $func;
    function __construct(callable $func,Config $conf = null)
    {
        $this->func = $func;
        if($conf == null){
            $conf = new Config();
        }
        parent::__construct($conf);
    }

    protected function createObject()
    {
        return call_user_func($this->func,$this->getConfig());
    }
}