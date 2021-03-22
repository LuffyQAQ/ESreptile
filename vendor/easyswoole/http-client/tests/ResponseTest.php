<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace EasySwoole\HttpClient\Test;


use EasySwoole\HttpClient\HttpClient;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    // 先启动server.php
    protected $url = 'http://127.0.0.1:9510';

    public function testJson()
    {
        $client = new HttpClient($this->url);
        $client->setQuery(['action' => 'json']);
        $response = $client->get();
        $array = ['title' => 'easyswoole', 'desc' => 'swoole framework'];

        $result = $response->json(true);
        $this->assertEquals($array, $result);

        $result = $response->json();
        $this->assertEquals((object)$array, $result);
    }

    public function testJsonp()
    {
        $client = new HttpClient($this->url);
        $client->setQuery(['action' => 'jsonp']);
        $response = $client->get();
        $array = ['title' => 'easyswoole', 'desc' => 'swoole framework'];

        $this->assertEquals('callback(' . json_encode($array) . ')', $response->getBody());

        $result = $response->jsonp(true);
        $this->assertEquals($array, $result);

        $result = $response->jsonp();
        $this->assertEquals((object)$array, $result);
    }

    public function testXml()
    {
        $client = new HttpClient($this->url);
        $client->setQuery(['action' => 'xml']);
        $response = $client->get();
        $array = ['title' => 'easyswoole', 'desc' => 'swoole framework'];
        $xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
        $xml .= "<test>\n";
        $xml .= "<title>easyswoole</title>\n";
        $xml .= "<desc>swoole framework</desc>\n";
        $xml .= "</test>\n";
        $this->assertEquals($xml, $response->getBody());

        $result = $response->xml(true);
        $this->assertEquals($array, $result);

        $result = $response->xml();
        $this->assertEquals((object)simplexml_load_string($xml, null, LIBXML_NOCDATA | LIBXML_COMPACT), $result);
    }
}