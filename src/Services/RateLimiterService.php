<?php
namespace Douyuxingchen\PhpSysAuth\Services;

use Douyuxingchen\PhpSysAuth\Caches\BaseCache;
use Douyuxingchen\PhpSysAuth\Enums\ErrCodeEnums;
use Douyuxingchen\PhpSysAuth\Exceptions\ErrCodeException;

class RateLimiterService {

    private $limitNumber;

    public function __construct($limitNumber)
    {
        $this->limitNumber = $limitNumber;
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
        $cache = BaseCache::getInstance();
        $lastRequestTime = $cache->get($key);

        if (!$lastRequestTime) {
            $cache->set($key, time(), 'EX', 1);
        } else {
            $timeDifference = time() - $lastRequestTime;
            $interval = 1 / $this->limitNumber;
            if ($timeDifference < $interval) {
                throw new ErrCodeException('Request too frequently', ErrCodeEnums::ERR_REQUEST_FREQUENTLY);
            } else {
                $cache->set($key, time(), 'EX', 1);
            }
        }
    }

}