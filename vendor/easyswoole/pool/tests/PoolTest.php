<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-06
 * Time: 22:47
 */

namespace EasySwoole\Pool\Tests;

use EasySwoole\Pool\Config;
use EasySwoole\Pool\MagicPool;
use EasySwoole\Pool\Manager;
use EasySwoole\Pool\Tests\Pool;
use PHPUnit\Framework\TestCase;

class PoolTest extends TestCase
{
    function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        //cli下关闭pool的自动定时检查
        parent::__construct($name, $data, $dataName);
    }

    function testNormalClass()
    {
        $manager = Manager::getInstance()->register(new Pool(new Config()), 'test');
        /**
         * @var $obj Pool
         */
        $obj = $manager->get('test');
        $this->assertEquals(PoolObject::class, $obj->getObj()->get());
    }

    function testNormalClass2()
    {
        $manager = Manager::getInstance()->register(new MagicPool(function () {
            return new PoolObject();
        }, new Config()), 'test');

        $pool = $manager->get('test');
        $obj = $pool->getObj();

        $this->assertEquals(PoolObject::class, $obj->get());
        $this->assertEquals(true, $pool->recycleObj($obj));
        /**
         * @var $obj PoolObject
         */
        $obj = $pool->getObj();
        $hash1 = $obj->__objHash;
        $this->assertEquals(PoolObject::class, $obj->get());
        $pool->recycleObj($obj);

        $obj = $pool->getObj();
        $hash2 = $obj->__objHash;
        $pool->recycleObj($obj);
        $this->assertEquals($pool->status()['created'], 1);
        $this->assertEquals($hash1, $hash2);

        $pool->invoke(function (PoolObject $object) {
            $this->assertEquals(PoolObject::class, $object->get());
        });
    }

    function testUnsetObj(){
        $manager = Manager::getInstance()->register(new MagicPool(function () {
            return new PoolObject(true);
        }, new Config()), 'test');
        $pool = $manager->get('test');
        $obj = $pool->getObj();
        $hash = $obj->__objHash;
        $this->assertEquals(false,$pool->isInPool($obj));
        $pool->recycleObj($obj);
        $this->assertEquals(true,$pool->isInPool($obj));

        $manager = Manager::getInstance()->register(new MagicPool(function () {
            return new PoolObject(false);
        }, new Config()), 'test2');
        $pool = $manager->get('test2');
        $obj = $pool->getObj();
        $this->assertEquals(null,$obj);
        $status = $pool->status();
        $this->assertEquals(0,$status['created']);
        $this->assertEquals(0,$status['inuse']);
    }
}
