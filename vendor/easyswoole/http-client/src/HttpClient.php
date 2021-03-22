<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/12/25
 * Time: 10:43 AM
 */

namespace EasySwoole\HttpClient;

use EasySwoole\HttpClient\Bean\CURLFile;
use EasySwoole\HttpClient\Bean\Response;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\HttpClient\Traits\Request;
use EasySwoole\HttpClient\Traits\UriManager;
use Swoole\Coroutine\Http\Client as CoroutineClient;
use Swoole\WebSocket\Frame;

class HttpClient
{
    // HTTP 1.0/1.1 标准请求方法
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_POST = 'POST';
    const METHOD_HEAD = 'HEAD';
    const METHOD_TRACE = 'TRACE';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_CONNECT = 'CONNECT';
    const METHOD_OPTIONS = 'OPTIONS';

    // 常用POST提交请求头
    const CONTENT_TYPE_TEXT_XML = 'text/xml';
    const CONTENT_TYPE_TEXT_JSON = 'text/json';
    const CONTENT_TYPE_FORM_DATA = 'multipart/form-data';
    const CONTENT_TYPE_APPLICATION_XML = 'application/xml';
    const CONTENT_TYPE_APPLICATION_JSON = 'application/json';
    const CONTENT_TYPE_X_WWW_FORM_URLENCODED = 'application/x-www-form-urlencoded';

    use UriManager;

    use Request;

    /** @var CoroutineClient */
    protected $client;

    /**
     * HttpClient constructor.
     * @param string|null $url
     * @throws InvalidUrl
     */
    public function __construct(?string $url = null)
    {
        $this->setUrl($url);
    }

    /**
     * 设置重定向次数 兼容以前版本的方法名称
     * @param int $maxRedirect
     * @return HttpClient
     */
    public function enableFollowLocation(int $maxRedirect = 5)
    {
        return $this->setFollowLocation($maxRedirect);
    }



    /**---------------------------------http请求---------------------------------------**/

    /**
     * 快速发起GET请求
     * 设置的请求头会合并到本次请求中
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function get(array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_GET);
    }

    /**
     * 快速发起HEAD请求
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function head(array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_HEAD);

    }

    /**
     * 快速发起TRACE请求
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function trace(array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_TRACE);

    }

    /**
     * 快速发起DELETE请求
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function delete(array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_DELETE);

    }

    // --------  以下四种方法可以设置请求BODY数据  --------

    /**
     * 快速发起PUT请求
     * @param null $data
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function put($data = null, array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_PUT, $data);
    }

    /**
     * 快速发起POST请求
     * @param null $data
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function post($data = null, array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_POST, $data);
    }

    /**
     * 快速发起PATCH请求
     * @param null $data
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function patch($data = null, array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_PATCH, $data);
    }

    /**
     * 快速发起预检请求
     * 需要自己设置预检头部
     * @param null $data
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function options($data = null, array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_OPTIONS, $data);
    }

    // -------- 针对POST方法另外给出两种快捷POST  --------

    /**
     * 快速发起XML POST
     * @param string|null $data 数据需要自己先行转为字符串
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function postXml(string $data = null, array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_POST, $data, HttpClient::CONTENT_TYPE_APPLICATION_XML);
    }

    /**
     * 快速发起JSON POST
     * @param string|null $data 数据需要自己先行转为字符串
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function postJson(string $data = null, array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_POST, $data, HttpClient::CONTENT_TYPE_APPLICATION_JSON);
    }

    /**
     * 下载文件 download 与 get 方法的不同是 download 收到数据后会写入到磁盘，而不是在内存中对 HTTP Body 进行拼接。因此 download 仅使用小量内存，就可以完成超大文件的下载。
     * @param string $filename
     * @param int $offset
     * @param string $httpMethod
     * @param null $rawData
     * @param null $contentType
     * @return Response|false
     * @throws InvalidUrl
     */
    public function download(string $filename, int $offset = 0, $httpMethod = HttpClient::METHOD_GET, $rawData = null, $contentType = null)
    {

        $client = $this->getClient();
        $client->setMethod($httpMethod);

        // 如果提供了数组那么认为是x-www-form-urlencoded快捷请求
        if (is_array($rawData)) {
            $rawData = http_build_query($rawData);
            $this->setContentType(HttpClient::CONTENT_TYPE_X_WWW_FORM_URLENCODED);
        }

        // 直接设置请求包体 (特殊格式的包体可以使用提供的Helper来手动构建)
        if (!empty($rawData)) {
            $client->setData($rawData);
            $this->setHeader('Content-Length', strlen($rawData));
        }

        // 设置ContentType(如果未设置默认为空的)
        if (!empty($contentType)) {
            $this->setContentType($contentType);
        }

        $client->setHeaders($this->getHeader());

        $response = $client->download($this->url->getFullPath(), $filename, $offset);
        return $response ? $this->createHttpResponse($client) : false;
    }

    /**
     * @param string $httpMethod
     * @param null $rawData
     * @param null $contentType
     * @return Response
     * @throws InvalidUrl
     */
    protected function rawRequest($httpMethod = HttpClient::METHOD_GET, $rawData = null, $contentType = null): Response
    {
        $client = $this->getClient();
        //预处理。合并cookie 和header
        $this->setMethod($httpMethod);
        $client->setMethod($httpMethod);

        $cookies = (array)$this->getCookies() + (array)$client->cookies;
        if ($cookies) {
            $client->setCookies($cookies);
        }


        if ($httpMethod == HttpClient::METHOD_POST) {
            if (is_array($rawData)) {
                foreach ($rawData as $key => $item) {
                    if ($item instanceof \CURLFile) {
                        $client->addFile($item->getFilename(), $key, $item->getMimeType(), $item->getPostFilename());
                        unset($rawData[$key]);
                    }
                    if ($item instanceof CURLFile) {
                        $client->addFile($item->getPath(), $item->getName(), $item->getType(), $item->getFilename(), $item->getOffset(), $item->getLength());
                        unset($rawData[$key]);
                    }
                }
                $client->setData($rawData);
            } else if ($rawData !== null) {
                $client->setData($rawData);
            }
        } else if ($rawData !== null) {
            $client->setData($rawData);
        }
        if (is_string($rawData)) {
            $this->setHeader('Content-Length', strlen($rawData));
        }
        if (!empty($contentType)) {
            $this->setContentType($contentType);
        }

        $headers = $this->getHeader();
        if ($headers) {
            $client->setHeaders($headers);
        }

        $client->execute($this->url->getFullPath());
        // 如果不设置保持长连接则直接关闭当前链接
        if (!isset($this->getClientSetting()['keep_alive']) || $this->getClientSetting()['keep_alive'] !== true) {
            $client->close();
        }
        // 处理重定向
        $redirected = $this->getRedirected();
        $followLocation = $this->getFollowLocation();
        if (($client->statusCode == 301 || $client->statusCode == 302) && (($followLocation > 0) && ($redirected < $followLocation))) {
            $this->setRedirected(++$redirected);
            $location = $client->headers['location'];
            $info = parse_url($location);
            // scheme 为空 没有域名
            if (empty($info['scheme']) && empty($info['host'])) {
                $this->url->setPath($location);
                $this->parserUrlInfo();
            } else {
                // 去除//开头的跳转域名
                $location = ltrim($location, '//');
                $this->setUrl($location);
                $this->client = null;
            }
            return $this->rawRequest($httpMethod, $rawData, $contentType);
        } else {
            $this->setRedirected(0);
        }
        return $this->createHttpResponse($client);
    }


    /**
     * 获取coroutine client
     * @return CoroutineClient
     * @throws InvalidUrl
     */
    public function getClient(): CoroutineClient
    {
        $url = $this->parserUrlInfo();
        if ($this->client instanceof CoroutineClient) {
            $this->client->host = $url->getHost();
            $this->client->port = $url->getPort();
            $this->client->ssl = $url->getIsSsl();
            $this->client->set($this->getClientSetting());
            return $this->client;
        }
        $this->client = new CoroutineClient($url->getHost(), $url->getPort(), $url->getIsSsl());
        return $this->getClient();
    }


    /**--------------------------------Websocket请求-----------------------------------------------*/

    /**
     * 升级为websocket请求
     * @param bool $mask
     * @return bool
     * @throws InvalidUrl
     */
    public function upgrade(bool $mask = true): bool
    {
        $this->setClientSetting('websocket_mask', $mask);
        $client = $this->getClient();
        $this->getCookies() && $client->setCookies($this->getCookies());
        $this->getHeader() && $client->setHeaders($this->getHeader());
        return $client->upgrade($this->url->getFullPath());
    }

    /**
     * 发送websocket数据
     * @param Frame|string $data
     * @param int $opcode
     * @param bool $finish
     * @return mixed
     * @throws InvalidUrl
     */
    public function push($data, int $opcode = WEBSOCKET_OPCODE_TEXT, bool $finish = true)
    {
        return $this->getClient()->push($data, $opcode, $finish);
    }

    /**
     * 接收websocket数据
     * @param float $timeout
     * @return Frame
     * @throws InvalidUrl
     */
    public function recv(float $timeout = 1.0)
    {
        return $this->getClient()->recv($timeout);
    }

    private function createHttpResponse(CoroutineClient $client): Response
    {
        $response = new Response((array)$client);
        $response->setClient($client);
        return $response;
    }

    public function setMethod($method)
    {
        $this->getClient()->setMethod($method);
        $this->method = $method;
        return $this;
    }

    public function __destruct()
    {
        if ($this->client instanceof CoroutineClient) {
            $this->client->close();
            $this->client = null;
        }
    }
}
