<?php
namespace Douyuxingchen\PhpSysAuth\Services;

use Douyuxingchen\PhpSysAuth\Caches\BaseCache;
use Douyuxingchen\PhpSysAuth\Enums\ErrCodeEnums;
use Douyuxingchen\PhpSysAuth\Exceptions\ErrCodeException;

class RateLimiterService {

    private $limitNumber;
    private $cache;

    public function __construct($limitNumber)
    {
        $this->limitNumber = $limitNumber;
        $this->cache = BaseCache::getInstance();
    }

    /**
     * @throws ErrCodeException
     */
    public function throttle($appKey)
    {
        // 如果是0则不限制请求频率
        if($this->limitNumber == 0) {
            return;
        }

        $key = 'throttle:' . $appKey;
        $lastRequestTime = $this->cache->get($key);

        if (!$lastRequestTime) {
            $this->cache->set($key, time(), 'EX', 1);
        } else {
            $timeDifference = time() - $lastRequestTime;
            $interval = 1 / $this->limitNumber;
            if ($timeDifference < $interval) {
                throw new ErrCodeException('Request too frequently', ErrCodeEnums::ERR_REQUEST_FREQUENTLY);
            } else {
                $this->cache->set($key, time(), 'EX', 1);
            }
        }
    }

    /**
     * @throws ErrCodeException
     */
    public function throttle2($appKey)
    {
        if ($this->limitNumber == 0) {
            return;
        }

        $key = 'throttle:' . $appKey;
        $requestTimes = $this->cache->get($key);

        if (!$requestTimes) {
            $requestTimes = [];
        }

        $currentTime = time();
        $requestTimes = array_filter($requestTimes, function ($time) use ($currentTime) {
            return $currentTime - $time < 1; // 限制在1秒内的请求记录
        });

        if (count($requestTimes) >= $this->limitNumber) {
            throw new ErrCodeException('Request too frequently', ErrCodeEnums::ERR_REQUEST_FREQUENTLY);
        }

        $requestTimes[] = $currentTime;
        $this->cache->set($key, $requestTimes);
    }

}