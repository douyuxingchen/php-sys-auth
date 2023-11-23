<?php
namespace Douyuxingchen\PhpSysAuth\Services;

use Douyuxingchen\PhpSysAuth\Caches\BaseCache;
use Douyuxingchen\PhpSysAuth\Enums\ErrCodeEnums;
use Douyuxingchen\PhpSysAuth\Exceptions\ValidationException;
use Douyuxingchen\PhpSysAuth\Models\SysAuthApp;
use Illuminate\Support\Facades\Config;

class AppService
{
    const CACHE_SEAT = '*';
    const CACHE_EXP = 3600;

    private $appData;

    /**
     * @throws ValidationException
     */
    public function createApp(string $appKey) : AppService
    {
        $isCache = (bool)Config::get('sys_auth.app.cache');
        $exp = (int)Config::get('sys_auth.app.exp');
        if(!$exp) {
            $exp = self::CACHE_EXP;
        }

        $cache = BaseCache::getInstance();
        if($isCache && $cache->exists(self::cacheKey($appKey))) {
            $cacheData = $cache->get(self::cacheKey($appKey));
            if($cacheData == self::CACHE_SEAT) {
                throw new ValidationException('AppKey not present or invalid', ErrCodeEnums::ERR_APP_KEY_EMPTY);
            }
            $this->appData = json_decode($cacheData, true);
            return $this;
        }

        $data = SysAuthApp::findByAppKey($appKey);
        if(empty($data)) {
            if($isCache) {
                $cache->set(self::cacheKey($appKey), self::CACHE_SEAT,'EX', 5);
            }
            throw new ValidationException('AppKey not present or invalid', ErrCodeEnums::ERR_APP_KEY_EMPTY);
        }

        if($isCache) {
            $cache->set(self::cacheKey($appKey), json_encode($data),'EX', $exp);
        }

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