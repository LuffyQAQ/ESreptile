<?php

namespace EasySwoole\Validate\test;

require_once 'BaseTestCase.php';

class LessThanWithColumnTest extends BaseTestCase
{
    function testValidCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('bar')->lessThanWithColumn('foo');
        $validateResult = $this->validate->validate(['bar' => 10, 'foo' => 11]);
        $this->assertTrue($validateResult);
    }

    function testDefaultErrorMsgCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('bar')->lessThanWithColumn('foo');
        $validateResult = $this->validate->validate(['bar' => 10, 'foo' => 9]);
        $this->assertFalse($validateResult);
        $this->assertEquals("bar必须小于foo的值", $this->validate->getError()->__toString());


        $this->freeValidate();
        $this->validate->addColumn('bar', 'Bar')->lessThanWithColumn('foo');
        $this->validate->addColumn('foo', 'Foo');
        $validateResult = $this->validate->validate(['bar' => 10, 'foo' => 9]);
        $this->assertFalse($validateResult);
        $this->assertEquals("Bar必须小于Foo的值", $this->validate->getError()->__toString());
    }

    // 自定义错误信息断言
    function testCustomErrorMsgCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('bar')->lessThanWithColumn('foo', 'bar不能大于foo');
        $validateResult = $this->validate->validate(['bar' => 10, 'foo' => 9]);
        $this->assertFalse($validateResult);
        $this->assertEquals("bar不能大于foo", $this->validate->getError()->__toString());
    }
}
