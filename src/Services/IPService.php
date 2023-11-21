<?php
namespace Douyuxingchen\PhpSysAuth\Services;

class IPService
{
    public static function getClientIP() {
        $ip = request()->ip();
        // 如果应用部署在代理后面，尝试获取原始客户端 IP 地址
        if (request()->server('HTTP_X_FORWARDED_FOR')) {
            $ip = explode(',', request()->server('HTTP_X_FORWARDED_FOR'))[0];
        } elseif (request()->server('HTTP_CLIENT_IP')) {
            $ip = request()->server('HTTP_CLIENT_IP');
        } elseif (request()->server('HTTP_X_REAL_IP')) {
            $ip = request()->server('HTTP_X_REAL_IP');
        }
        return $ip;
    }
}




