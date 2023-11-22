<?php

namespace Douyuxingchen\PhpSysAuth\Services;

use Douyuxingchen\PhpSysAuth\Caches\BaseCache;
use Douyuxingchen\PhpSysAuth\Models\SysAuthApi;
use Douyuxingchen\PhpSysAuth\Models\SysAuthAppRoute;

class AppRouteService
{

    public function getRouteList($appID, $appKey, $exp)
    {
        $cache = BaseCache::getInstance();
        if($cache->exists(self::cacheKey($appKey))) {
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
        $cache->set(self::cacheKey($appKey), json_encode($routes), 'EX', $exp);

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