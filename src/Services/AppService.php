<?php
namespace Douyuxingchen\PhpSysAuth\Services;

use Douyuxingchen\PhpSysAuth\Caches\BaseCache;
use Douyuxingchen\PhpSysAuth\Enums\ErrCodeEnums;
use Douyuxingchen\PhpSysAuth\Exceptions\ValidationException;
use Douyuxingchen\PhpSysAuth\Models\SysAuthApp;

class AppService
{
    private $appData;

    /**
     * @throws ValidationException
     */
    public function createApp(string $appKey) : AppService
    {
        $cache = BaseCache::getInstance();
        if($cache->exists(self::cacheKey($appKey))) {
            $this->appData = json_decode($cache->get(self::cacheKey($appKey)), true);
            return $this;
        }

        $sysAuthApp = new SysAuthApp('xx');
        $data = $sysAuthApp->query()->where('app_key', $appKey)->first();
        if(empty($data)) {
            throw new ValidationException('AppKey not present or invalid', ErrCodeEnums::ERR_APP_KEY_EMPTY);
        }
        $cache->set(self::cacheKey($appKey), json_encode($data),'EX', 60);
        $this->appData = $data->toArray();
        return $this;
    }

    public function delCache(string $appKey)
    {
        $cache = BaseCache::getInstance();
        return $cache->del(self::cacheKey($appKey));
    }

    private static function cacheKey(string $appKey) : string
    {
        return sprintf('app:%s', $appKey);
    }

    public function getAppID()
    {
        return $this->appData['id'];
    }

    public function getAppKey()
    {
        return $this->appData['app_key'];
    }

    public function getSecretKey()
    {
        return $this->appData['secret_key'];
    }

    public function getApiLimit()
    {
        return $this->appData['api_limit'];
    }

    public function getIpLimitType()
    {
        return $this->appData['ip_limit_type'];
    }

    public function getIpWhiteList()
    {
        return $this->appData['ip_white_list'];
    }

    public function getIpBlackList()
    {
        return $this->appData['ip_black_list'];
    }

    public function getStatus()
    {
        return $this->appData['status'];
    }

}