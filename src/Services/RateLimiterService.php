<?php
namespace Douyuxingchen\PhpSysAuth\Services;

use Douyuxingchen\PhpSysAuth\Caches\BaseCache;
use Douyuxingchen\PhpSysAuth\Enums\ErrCodeEnums;
use Douyuxingchen\PhpSysAuth\Exceptions\ErrCodeException;
use RedisException;

class RateLimiterService {

    const RateType = ['counter', 'window', 'bucket'];

    private $limitNumber;
    private $cache;

    public function __construct($limitNumber)
    {
        $this->limitNumber = $limitNumber;
        $this->cache = BaseCache::getInstance();
    }

    public function counter($appKey) {
        $key = 'limit:counter:' . $appKey;
        $currentCount = $this->cache->incr($key);
        if ($currentCount > $this->limitNumber) {
            throw new ErrCodeException('Request too frequently', ErrCodeEnums::ERR_REQUEST_FREQUENTLY);
        }
        $this->cache->expire($key, 1);
    }

    public function window($appKey)
    {
        $nowTime = time();
        $startTime = $nowTime - 1;
        $zSetKey = 'limit:window:' . $appKey;
        $requestHistory = $this->cache->zRangeByScore($zSetKey, $startTime, $nowTime);
        $count = count($requestHistory);

        if ($count >= $this->limitNumber) {
            throw new ErrCodeException('Request too frequently', ErrCodeEnums::ERR_REQUEST_FREQUENTLY);
        }

        $value = $appKey . ':' . $nowTime . uniqid();
        $this->cache->zAdd($zSetKey, $nowTime, $value);
        $this->cache->expire($zSetKey, 1);
    }

    public function bucket($appKey) {
        $key = 'limit:bucket:' . $appKey;
        $currentTokens = $this->cache->lLen($key);
        if ($currentTokens >= $this->limitNumber) {
            throw new ErrCodeException('Request too frequently', ErrCodeEnums::ERR_REQUEST_FREQUENTLY);
        }
        $this->cache->rPush($key, time()); // 添加令牌
        $this->cache->expire($key, 1);
    }

    public function counterEval($appKey) {
        $key = 'limit:counterEval:' . $appKey;
        $luaScript = "
            local currentCount = redis.call('INCR', KEYS[1])
            if tonumber(currentCount) > tonumber(ARGV[1]) then
                return redis.error_reply('Request too frequently')
            end
            redis.call('EXPIRE', KEYS[1], 1)
            return currentCount
        ";

        try{
            $this->cache->eval($luaScript, 1, $key, $this->limitNumber);
        }catch (RedisException $e){
            throw new ErrCodeException($e->getMessage(), ErrCodeEnums::ERR_REQUEST_FREQUENTLY);
        }
    }


    public function windowEval($appKey)
    {
        $luaScript = "
            local zSetKey = KEYS[1]
            local cacheVal = ARGV[1]
            local limit = tonumber(ARGV[2])
            local startTime = ARGV[3]
            local nowTime = ARGV[4]
    
            local requestHistory = redis.call('ZRANGEBYSCORE', zSetKey, startTime, nowTime)
            local count = table.getn(requestHistory)
    
            if count >= limit then
                return -1
            else
                redis.call('ZADD', zSetKey, nowTime, cacheVal)
                redis.call('EXPIRE', zSetKey, 1)
                return count
            end
        ";

        $nowTime = time();
        $startTime = $nowTime - 1;
        $zSetKey = 'limit:windowEval:' . $appKey;
        $value = $appKey . ':' . $nowTime . uniqid();

        $result =  $this->cache->eval($luaScript, 1, $zSetKey, $value, $this->limitNumber,
            $startTime, $nowTime
        );

        if ($result === -1) {
            throw new ErrCodeException('Request too frequently', ErrCodeEnums::ERR_REQUEST_FREQUENTLY);
        }
    }

    public function bucketEval($appKey) {
        $key = 'limit:bucketEval:' . $appKey;
        $luaScript = "
            local currentTokens = redis.call('LLEN', KEYS[1])
            if tonumber(currentTokens) >= tonumber(ARGV[1]) then
                return redis.error_reply('Request too frequently')
            end
            redis.call('RPUSH', KEYS[1], ARGV[2])
            redis.call('EXPIRE', KEYS[1], 1)
            return currentTokens
        ";

        try{
            $this->cache->eval($luaScript, 1, $key, $this->limitNumber, time());
        }catch (RedisException $e){
            throw new ErrCodeException($e->getMessage(), ErrCodeEnums::ERR_REQUEST_FREQUENTLY);
        }
    }


}