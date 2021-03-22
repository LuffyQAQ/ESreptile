<?php


namespace EasySwoole\Pool;


interface ObjectInterface
{
    //unset 的时候执行
    function gc();
    //使用后,free的时候会执行
    function objectRestore();
    //使用前调用,当返回true，表示该对象可用。返回false，该对象失效，需要回收
    function beforeUse():?bool ;
}