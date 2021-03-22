<?php


namespace EasySwoole\Pool;


use EasySwoole\Pool\Exception\Exception;
use EasySwoole\Spl\SplBean;

class Config extends SplBean
{
    protected $intervalCheckTime = 15*1000;
    protected $maxIdleTime = 10;
    protected $maxObjectNum = 20;
    protected $minObjectNum = 5;
    protected $getObjectTimeout = 3.0;
    protected $loadAverageTime = 0.001;

    protected $extraConf;

    /**
     * @return float|int
     */
    public function getIntervalCheckTime()
    {
        return $this->intervalCheckTime;
    }

    /**
     * @param $intervalCheckTime
     * @return Config
     */
    public function setIntervalCheckTime($intervalCheckTime): Config
    {
        $this->intervalCheckTime = $intervalCheckTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxIdleTime(): int
    {
        return $this->maxIdleTime;
    }

    /**
     * @param int $maxIdleTime
     * @return Config
     */
    public function setMaxIdleTime(int $maxIdleTime): Config
    {
        $this->maxIdleTime = $maxIdleTime;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxObjectNum(): int
    {
        return $this->maxObjectNum;
    }

    public function setMaxObjectNum(int $maxObjectNum): Config
    {
        if($this->minObjectNum >= $maxObjectNum){
            throw new Exception('min num is bigger than max');
        }
        $this->maxObjectNum = $maxObjectNum;
        return $this;
    }

    /**
     * @return float
     */
    public function getGetObjectTimeout(): float
    {
        return $this->getObjectTimeout;
    }

    /**
     * @param float $getObjectTimeout
     * @return Config
     */
    public function setGetObjectTimeout(float $getObjectTimeout): Config
    {
        $this->getObjectTimeout = $getObjectTimeout;
        return $this;
    }

    public function getExtraConf()
    {
        return $this->extraConf;
    }

    /**
     * @param $extraConf
     * @return Config
     */
    public function setExtraConf($extraConf): Config
    {
        $this->extraConf = $extraConf;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinObjectNum(): int
    {
        return $this->minObjectNum;
    }

    /**
     * @return float
     */
    public function getLoadAverageTime(): float
    {
        return $this->loadAverageTime;
    }

    /**
     * @param float $loadAverageTime
     * @return Config
     */
    public function setLoadAverageTime(float $loadAverageTime): Config
    {
        $this->loadAverageTime = $loadAverageTime;
        return $this;
    }

    public function setMinObjectNum(int $minObjectNum): Config
    {
        if($minObjectNum >= $this->maxObjectNum){
            throw new Exception('min num is bigger than max');
        }
        $this->minObjectNum = $minObjectNum;
        return $this;
    }
}