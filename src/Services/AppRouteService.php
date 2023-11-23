<?php

namespace Douyuxingchen\PhpSysAuth\Services;

use Douyuxingchen\PhpSysAuth\Caches\BaseCache;
use Douyuxingchen\PhpSysAuth\Models\SysAuthApi;
use Douyuxingchen\PhpSysAuth\Models\SysAuthAppRoute;
use Illuminate\Support\Facades\Config;

class AppRouteService
{
    const CACHE_EXP = 3600;

    public function getRouteList($appID, $appKey)
    {
        $isCache = (bool)Config::get('sys_auth.app.cache');
        $exp = (int)Config::get('sys_auth.app.exp');
        if(!$exp) {
            $exp = self::CACHE_EXP;
        }

        $cache = BaseCache::getInstance();
        if($isCache && $cache->exists(self::cacheKey($appKey))) {
            return json_decode($cache->get(self::cacheKey($appKey)), true);
        }

        $apiIds = (new SysAuthAppRoute())->query()
            ->select('api_id')
            ->where('app_id', $appID)
            ->pluck('api_id')
            ->toArray();
        $routes = (new SysAuthApi())->query()
            ->select('route')
            ->whereIn('id', $apiIds)
            ->pluck('route')
            ->toArray();

        if(empty($routes)) {
            return [];
        }

        foreach ($routes as $k => $v) {
            $routes[$k] = strtolower($v);
        }

        if($isCache) {
            $cache->set(self::cacheKey($appKey), json_encode($routes), 'EX', $exp);
        }

        return $routes;
    }

    public function delCache(string $appKey)
    {
        $cache = BaseCache::getInstance();
        return $cache->del(self::cacheKey($appKey));
    }

    private static function cacheKey(string $appKey) : string
    {
        return sprintf('appRoute:%s', $appKey);
    }

}