<?php

namespace EasySwoole\Validate;

use EasySwoole\Spl\SplArray;

interface ValidateInterface
{
    /**
     * 返回当前校验规则的名字
     * @return string
     */
    public function name(): string;

    /**
     * 检验失败返回错误信息即可
     *
     * @param SplArray $spl
     * @param string $column
     * @param mixed ...$args
     * @return string|null
     */
    public function validate(SplArray $spl, $column, ...$args): ?string;
}