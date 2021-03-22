<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/12/25
 * Time: 1:28 PM
 */

namespace EasySwoole\HttpClient\Bean;

use EasySwoole\Spl\SplBean;
use Swoole\Coroutine\Http\Client;

/**
 * 标准响应体
 * Class Response
 * @package EasySwoole\HttpClient\Bean
 */
class Response extends SplBean
{
    protected $headers;
    protected $body;
    protected $errCode;
    protected $errMsg;
    protected $statusCode;
    protected $set_cookie_headers;
    protected $cookies;
    protected $connected;
    protected $host;
    protected $port;
    protected $ssl;
    protected $setting;
    protected $requestMethod;
    protected $requestHeaders;
    protected $requestBody;
    protected $uploadFiles;
    protected $downloadFile;
    protected $downloadOffset;

    protected $client;

    /**
     * Headers Getter
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Body Getter
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * ErrCode Getter
     * @return mixed
     */
    public function getErrCode()
    {
        return $this->errCode;
    }

    /**
     * ErrMsg Getter
     * @return mixed
     */
    public function getErrMsg()
    {
        return $this->errMsg;
    }

    /**
     * StatusCode Getter
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * SetCookieHeaders Getter
     * @return mixed
     */
    public function getSetCookieHeaders()
    {
        return $this->set_cookie_headers;
    }

    /**
     * Cookies Getter
     * @return mixed
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Connected Getter
     * @return mixed
     */
    public function getConnected()
    {
        return $this->connected;
    }

    /**
     * Host Getter
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Port Getter
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Ssl Getter
     * @return mixed
     */
    public function getSsl()
    {
        return $this->ssl;
    }

    /**
     * Setting Getter
     * @return mixed
     */
    public function getSetting()
    {
        return $this->setting;
    }

    /**
     * RequestMethod Getter
     * @return mixed
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * RequestHeaders Getter
     * @return mixed
     */
    public function getRequestHeaders()
    {
        return $this->requestHeaders;
    }

    /**
     * RequestBody Getter
     * @return mixed
     */
    public function getRequestBody()
    {
        return $this->requestBody;
    }

    /**
     * UploadFiles Getter
     * @return mixed
     */
    public function getUploadFiles()
    {
        return $this->uploadFiles;
    }

    /**
     * DownloadFile Getter
     * @return mixed
     */
    public function getDownloadFile()
    {
        return $this->downloadFile;
    }

    /**
     * DownloadOffset Getter
     * @return mixed
     */
    public function getDownloadOffset()
    {
        return $this->downloadOffset;
    }

    /**
     * Client Getter
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Client Setter
     * @param mixed $client
     */
    public function setClient($client): void
    {
        $this->client = $client;
    }

    /**
     * 获取json格式的内容
     * @param bool $assoc true返回数组 false返回对象
     * @return mixed
     */
    public function json($assoc = false)
    {
        return json_decode($this->body, $assoc);
    }

    /**
     * 获取jsonp格式的内容
     * @param bool $assoc true返回数组 false返回对象
     * @return mixed
     */
    public function jsonp($assoc = false)
    {
        $jsonp = trim($this->body);
        if (isset($jsonp[0]) && $jsonp[0] !== '[' && $jsonp[0] !== '{') {
            $begin = strpos($jsonp, '(');
            $end = strrpos($jsonp, ')');
            if (false !== $begin && false !== $end) {
                $jsonp = substr($jsonp, $begin + 1, $end - $begin - 1);
            }
        }
        return json_decode($jsonp, $assoc);
    }

    /**
     * 获取xml格式的内容
     * @see https://www.w3.org/TR/2008/REC-xml-20081126/#charsets - XML charset range
     * @see http://php.net/manual/en/regexp.reference.escape.php - escape in UTF-8 mode
     * @param bool $assoc true返回数组 false返回对象
     * @return array|object
     */
    public function xml($assoc = false)
    {

        $backup = libxml_disable_entity_loader(true);

        $xml = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]+/u', '', $this->body);

        $result = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_NOCDATA | LIBXML_NOBLANKS);

        libxml_disable_entity_loader($backup);

        if ($assoc) {
            $result = (array)$result;
        }
        return $result;
    }
}
