<?php


namespace EasySwoole\HttpClient\Traits;


use EasySwoole\HttpClient\HttpClient;

trait Request
{
    /**
     * 请求携带的Cookies
     * @var array
     */
    protected $cookies = [];


    /**
     * 默认请求头
     * @var array
     */
    protected $header = [
        "user-agent" => 'EasySwooleHttpClient/0.1',
        'accept' => '*/*',
        'pragma' => 'no-cache',
        'cache-control' => 'no-cache'
    ];

    protected $followLocation = 3;

    protected $redirected = 0;

    /**
     * 请求方法
     * @var string
     */
    protected $method = HttpClient::METHOD_GET;


    /**
     * @return int
     */
    public function getFollowLocation(): int
    {
        return $this->followLocation;
    }

    /**
     * 重定向次数
     * @param int $followLocation
     * @return $this
     */
    public function setFollowLocation(int $followLocation)
    {
        $this->followLocation = $followLocation;
        return $this;
    }

    /**
     * 记录重定向多少次
     * @return int
     */
    public function getRedirected(): int
    {
        return $this->redirected;
    }

    /**
     * @param int $redirected
     * @return $this
     */
    public function setRedirected(int $redirected)
    {
        $this->redirected = $redirected;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeader(): array
    {
        return $this->header;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    /**
     * 设置cookie
     * @param array $cookies
     * @return $this
     */
    public function setCookies(array $cookies)
    {
        $this->cookies = $cookies;
        return $this;
    }

    /**
     * @param array $header
     * @param bool $isMerge
     * @param bool $strtolower
     * @return $this
     */
    public function setHeaders(array $header, $isMerge = true, $strtolower = true)
    {
        if (empty($header)) {
            return $this;
        }

        // 非合并模式先清空当前的Header再设置
        if (!$isMerge) {
            $this->header = [];
        }

        foreach ($header as $name => $value) {
            $this->setHeader($name, $value, $strtolower);
        }
        return $this;
    }

    /**
     * @param string $userName
     * @param string $password
     * @return $this
     */
    public function setBasicAuth(string $userName, string $password)
    {
        $basicAuthToken = base64_encode("{$userName}:{$password}");
        $this->setHeader('Authorization', "Basic {$basicAuthToken}", false);
        return $this;
    }

    public function setHeader(string $key, string $value, $strtolower = true)
    {
        if ($strtolower) {
            $this->header[strtolower($key)] = strtolower($value);
        } else {
            $this->header[$key] = $value;
        }
        return $this;
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
        return $this;
    }

    public function setContentType(string $contentType)
    {
        return $this->setHeader('content-type', $contentType);
    }

    public function addCookie(string $key, string $value)
    {
        $this->cookies[$key] = $value;
        return $this;
    }

    public function addCookies(array $cookies, $isMerge = true)
    {

        if ($isMerge) {  // 合并配置项到当前配置中
            foreach ($cookies as $name => $value) {
                $this->cookies[$name] = $value;
            }
        } else {
            $this->cookies = $cookies;
        }
        return $this;
    }

    /**
     * 设置为Xml请求
     * @return $this
     */
    public function setContentTypeXml()
    {
        $this->setContentType(HttpClient::CONTENT_TYPE_APPLICATION_XML);
        return $this;
    }

    /**
     * 设置为FromData请求
     * @return $this
     */
    public function setContentTypeFormData()
    {
        $this->setContentType(HttpClient::CONTENT_TYPE_FORM_DATA);
        return $this;
    }

    /**
     * 设置为FromUrlencoded请求
     * @return $this
     */
    public function setContentTypeFormUrlencoded()
    {
        $this->setContentType(HttpClient::CONTENT_TYPE_X_WWW_FORM_URLENCODED);
        return $this;
    }

    /**
     * 设置为XMLHttpRequest请求
     * @return $this
     */
    public function setXMLHttpRequest()
    {
        $this->setHeader('x-requested-with', 'xmlhttprequest');
        return $this;
    }

    /**
     * 设置为Json请求
     * @return $this
     */
    public function setContentTypeJson()
    {
        $this->setContentType(HttpClient::CONTENT_TYPE_APPLICATION_JSON);
        return $this;
    }

    /**
     * 协程客户端设置项
     * @var array
     */
    protected $clientSetting = [];

    /**
     * @return array
     */
    public function getClientSetting(): array
    {
        return $this->clientSetting;
    }

    /**
     * 总超时，包括连接、发送、接收所有超时
     * @param float $timeout
     * @return $this
     */
    public function setTimeout(float $timeout)
    {
        $this->clientSetting['timeout'] = $timeout;
        return $this;
    }

    /**
     * 连接超时，会覆盖第一个总的 timeout
     * @param float $connectTimeout
     * @return $this
     */
    public function setConnectTimeout(float $connectTimeout)
    {
        $this->clientSetting['connect_timeout'] = $connectTimeout;
        return $this;
    }

    /**
     * 接收超时，会覆盖第一个总的 timeout
     * @param float $readTimeout
     * @return $this
     */
    public function setReadTimeout(float $readTimeout)
    {
        $this->clientSetting['read_timeout'] = $readTimeout;
        return $this;
    }

    /**
     * 发送超时，会覆盖第一个总的 timeout
     * @param float $writeTimeout
     * @return $this
     */
    public function setWriteTimeout(float $writeTimeout)
    {
        $this->clientSetting['write_timeout'] = $writeTimeout;
        return $this;
    }

    /**
     * 长连接
     * @param bool $keepAlive
     * @return $this
     */
    public function setKeepAlive(bool $keepAlive = true)
    {
        $this->clientSetting['keep_alive'] = $keepAlive;
        return $this;
    }

    /**
     * 验证服务端证书
     * @param bool $sslVerifyPeer
     * @param false $sslAllowSelfSigned 允许自签名证书
     * @return $this
     */
    public function setSslVerifyPeer(bool $sslVerifyPeer = true, $sslAllowSelfSigned = false)
    {
        $this->clientSetting['ssl_verify_peer'] = $sslVerifyPeer;
        $this->clientSetting['ssl_allow_self_signed'] = $sslAllowSelfSigned;
        return $this;
    }

    /**
     * 设置服务器主机名称
     * @param string $sslHostName
     * @return $this
     */
    public function setSslHostName(string $sslHostName)
    {
        $this->clientSetting['ssl_host_name'] = $sslHostName;
        return $this;
    }

    /**
     * 设置 ssl_verify_peer 为 true 时，用来验证远端证书所用到的 CA 证书。本选项值为 CA 证书在本地文件系统的全路径及文件名
     * @param string $sslCafile
     * @return $this
     */
    public function setSslCafile(string $sslCafile)
    {
        $this->clientSetting['ssl_cafile'] = $sslCafile;
        return $this;
    }

    /**
     * 如果未设置 ssl_cafile，或者 ssl_cafile 所指的文件不存在时，会在 ssl_capath 所指定的目录搜索适用的证书。该目录必须是已经经过哈希处理的证书目录。
     * @param string $sslCapath
     * @return $this
     */
    public function setSslCapath(string $sslCapath)
    {
        $this->clientSetting['ssl_capath'] = $sslCapath;
        return $this;
    }

    /**
     * ssl cert
     * @param string $sslCertFile
     * @return $this
     */
    public function setSslCertFile(string $sslCertFile)
    {
        $this->clientSetting['ssl_cert_file'] = $sslCertFile;
        return $this;
    }

    /**
     * ssl key
     * @param string $sslKeyFile
     * @return $this
     */
    public function setSslKeyFile(string $sslKeyFile)
    {
        $this->clientSetting['ssl_key_file'] = $sslKeyFile;
        return $this;
    }

    /**
     * 本地证书 ssl_cert_file 文件的密码
     * @param $sslPassphrase
     * @return $this
     */
    public function setSslPassphrase($sslPassphrase)
    {
        $this->clientSetting['ssl_passphrase'] = $sslPassphrase;
        return $this;
    }

    /**
     * http_proxy
     * @param string $proxyHost
     * @param int $proxyPort
     * @param string|null $proxyUser
     * @param string|null $proxyPass
     * @return $this
     */
    public function setProxyHttp(string $proxyHost, int $proxyPort, string $proxyUser = null, string $proxyPass = null)
    {
        $this->clientSetting['http_proxy_host'] = $proxyHost;
        $this->clientSetting['http_proxy_port'] = $proxyPort;

        if (!empty($proxyUser)) {
            $this->clientSetting['http_proxy_user'] = $proxyUser;
        }

        if (!empty($proxyPass)) {
            $this->clientSetting['http_proxy_password'] = $proxyPass;
        }
        return $this;
    }

    /**
     * socket5 代理
     * @param string $proxyHost
     * @param int $proxyPort
     * @param string|null $proxyUser
     * @param string|null $proxyPass
     * @return $this
     */
    public function setProxySocks5(string $proxyHost, int $proxyPort, string $proxyUser = null, string $proxyPass = null)
    {
        $this->clientSetting['socks5_host'] = $proxyHost;
        $this->clientSetting['socks5_port'] = $proxyPort;

        if (!empty($proxyUser)) {
            $this->clientSetting['socks5_username'] = $proxyUser;
        }

        if (!empty($proxyPass)) {
            $this->clientSetting['socks5_password'] = $proxyPass;
        }
        return $this;
    }

    /**
     * 机器有多个网卡的情况下，设置 bind_address 参数可以强制客户端 Socket 绑定某个网络地址。设置 bind_port 可以使客户端 Socket 使用固定的端口连接到外网服务器。
     * @param string $bindAddress
     * @param int $bindPort
     * @return $this
     */
    public function setSocketBind(string $bindAddress, int $bindPort)
    {
        $this->clientSetting['bind_address'] = $bindAddress;
        $this->clientSetting['bind_port'] = $bindPort;
        return $this;
    }

    public function setClientSetting(string $key, $setting)
    {
        $this->clientSetting[$key] = $setting;
        return $this;
    }

    public function setClientSettings(array $settings, $isMerge = true)
    {
        if ($isMerge) {  // 合并配置项到当前配置中
            foreach ($settings as $name => $value) {
                $this->clientSetting[$name] = $value;
            }
        } else {
            $this->clientSetting = $settings;
        }
        return $this;
    }
}
