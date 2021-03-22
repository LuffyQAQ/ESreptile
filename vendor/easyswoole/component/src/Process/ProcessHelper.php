<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018-12-27
 * Time: 11:36
 */

namespace EasySwoole\Component\Process;


use Swoole\Server;

class ProcessHelper
{
    static function register(Server $server,AbstractProcess $process):bool
    {
        return $server->addProcess($process->getProcess());
    }
}