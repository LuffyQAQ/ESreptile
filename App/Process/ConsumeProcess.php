<?php


namespace App\Process;


use App\Utility\Config;
use App\Utility\RedisQueue;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\HttpClient\HttpClient;
use EasySwoole\Utility\File;

const CONSUME = 'consume_queue';

class ConsumeProcess extends AbstractProcess
{

    protected function run($arg)
    {
        // TODO: Implement run() method.
        go(function (){
            $config = Config::getInstance();
            for ($i = 1; $i <= $config->getConsumeCoroutineNum(); $i++)
            {
                go(function () use ($config){
                    while (1)
                    {
                        $data = RedisQueue::getInstance()->pop(CONSUME);
                        if(empty($data))
                        {
                            \EasySwoole\EasySwoole\Logger::getInstance()->console(CONSUME.' 暂无可消费队列');
                            \co::sleep(1);
                            continue;
                        }
                        $data = json_decode($data,true);
                        \EasySwoole\EasySwoole\Logger::getInstance()->console(CONSUME.' 正在处理 '.$data['alt']);
                        $pathInfo = pathinfo($data['src']);
                        $path = EASYSWOOLE_ROOT.'/Images/'.date('Y-m-d').'/'.$data['alt'].'.'.$pathInfo['extension'];
                        $httpClient = new HttpClient($data['src']);
                        File::createFile($path,'');
                        $suc = $httpClient->download($path);

                        \EasySwoole\EasySwoole\Logger::getInstance()->console($data['alt'].'--'.$suc ? '下载成功':'下载失败');


                    }

                });
            }
        });

    }
}