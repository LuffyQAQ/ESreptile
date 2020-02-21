<?php


namespace App\Utility;


use EasySwoole\Component\Singleton;

class Config
{
    use Singleton;

    //爬取网站的url
    protected $startUrl;

    //生产者协程数量
    protected $productCoroutineNum=3;
    //消费者协程数量
    protected $consumeCoroutineNum=3;

    /**
     * @return mixed
     */
    public function getStartUrl()
    {
        return $this->startUrl;
    }

    /**
     * @param mixed $startUrl
     */
    public function setStartUrl($startUrl)
    {
        $this->startUrl = $startUrl;
        return $this;
    }
    /**
     * @return int
     */
    public function getProductCoroutineNum()
    {
        return $this->productCoroutineNum;
    }

    /**
     * @param int $productCoroutineNum
     */
    public function setProductCoroutineNum($productCoroutineNum)
    {
        $this->productCoroutineNum = $productCoroutineNum;
        return $this;
    }

    /**
     * @return int
     */
    public function getConsumeCoroutineNum()
    {
        return $this->consumeCoroutineNum;
    }

    /**
     * @param int $consumeCoroutineNum
     */
    public function setConsumeCoroutineNum($consumeCoroutineNum)
    {
        $this->consumeCoroutineNum = $consumeCoroutineNum;
        return $this;
    }




}