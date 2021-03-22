<?php
/**
 * Created by PhpStorm.
 * User: Heelie
 * Date: 19-12-31
 * Time: 下午19:35
 */

namespace EasySwoole\Validate\test;

use EasySwoole\Spl\SplArray;
use EasySwoole\Validate\ValidateInterface;

require_once 'BaseTestCase.php';

class CustomValidator implements ValidateInterface
{
    /**
     * 返回当前校验规则的名字
     * @return string
     */
    public function name(): string
    {
        return 'mobile';
    }

    /**
     * 检验失败返回错误信息即可
     * @param SplArray $spl
     * @param $column
     * @param mixed ...$args
     * @return string|null
     */
    public function validate(SplArray $spl, $column, ...$args): ?string
    {
        $regular = '/^((13[0-9])|(14[5,7,9])|(15[^4])|(18[0-9])|(17[0,1,3,5,6,7,8]))\\d{8}$/';
        if (!preg_match($regular, $spl->get($column))) {
            return '手机号验证未通过';
        }
        return null;
    }
}

class CallUserRuleTest extends BaseTestCase
{
    // 合法断言
    function testValidCase()
    {
        $this->freeValidate();
        $this->validate->addColumn('mobile')->callUserRule(new CustomValidator);
        $validateResult = $this->validate->validate([
            'mobile' => '13312345678',
        ]);
        $this->assertTrue($validateResult);
    }

    // 默认错误信息断言
    function testDefaultErrorMsgCase()
    {
        // 手机号验证不通过
        $this->freeValidate();
        $this->validate->addColumn('mobile')->callUserRule(new CustomValidator);
        $validateResult = $this->validate->validate(['mobile' => '12312345678']);
        $this->assertFalse($validateResult);
        $this->assertEquals('手机号验证未通过', $this->validate->getError()->__toString());
    }

    // 自定义错误信息断言
    function testCustomErrorMsgCase()
    {
        // 日期相等
        $this->freeValidate();
        $this->validate->addColumn('mobile')->callUserRule(new CustomValidator, '手机号格式错误');
        $validateResult = $this->validate->validate(['mobile' => '12312345678']);
        $this->assertFalse($validateResult);
        $this->assertEquals('手机号格式错误', $this->validate->getError()->__toString());
    }
}