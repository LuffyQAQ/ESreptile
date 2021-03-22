<?php


namespace App\Process;


use App\Utility\Config;
use App\Utility\RedisQueue;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\HttpClient\HttpClient;
use QL\QueryList;

const QueueName = 'product_queue';

class ProductProcess extends AbstractProcess
{

    protected function run($arg)
    {

        // TODO: Implement run() method.
        go(function (){
            $config = Config::getInstance();
            RedisQueue::getInstance()->push(QueueName,$config->getStartUrl());

            for ($i = 1; $i <= $config->getProductCoroutineNum(); $i++)
            {
                go(function () use ($config){
                    while (1)
                    {
                        //从队列中区出地址
                        $url = RedisQueue::getInstance()->pop(QueueName);
                        if(empty($url))
                        {
                            \co::sleep(1);
                            continue;
                        }

                        // 通过http协程客户端拿到地址内容
                        $httpClient = new HttpClient($url);
                        $body = $httpClient->get()->getBody();
                        if (empty($body)) {
                            \co::sleep(1);
                            continue;
                        }
                        // 开始生产,根据内容，设置规则
                        libxml_use_internal_errors(true);

                        //处理中文乱码内容
                        $body =iconv('GBK','UTF-8',$body);


                        $ql = QueryList::getInstance()->html($body);

                        $rules = [
                            'src' => ['img', 'src'],
                            'alt' => ['img', 'alt'],
                        ];
                        $nextUrl = $ql->find('.page .prev')->eq(1)->attr('href');
                        $imgList = $ql->rules($rules)->range('.list li')->query()->getData()->all();
                        foreach ($imgList as $img)
                        {
                            RedisQueue::getInstance()->push(CONSUME,$img);
                        }
                        //要爬取的页数,没有则停止生产
                        if(empty($nextUrl))
                        {
                            \co::sleep(1);
                            continue;
                        }
                        Logger::getInstance()->console($nextUrl);
                        //页面中的爬取链接不带host，要拼接上
                        RedisQueue::getInstance()->push(QueueName,'http://www.netbian.com'.$nextUrl);
                        \co::sleep(0.5);
                    }


                });
            }


        });
    }
}