<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2020/4/8 0008
 * Time: 11:29
 */

namespace EasySwoole\Pool\Tests;


use EasySwoole\Pool\AbstractPool;

class Pool extends AbstractPool
{
    protected function createObject()
    {
        return new PoolObject();
        // TODO: Implement createObject() method.
    }




}
