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


    /**
     * 验证IP是否符合规则
     *
     * 所有IPv4地址：0.0.0.0/0
     * 指定单个IP：如192.168.1.1
     * 指定CIDR段：如192.168.1.0/24
     *
     * @param string $ip
     * @param string $confIP
     * @return bool
     */
    public static function validateIP(string $ip, string $confIP): bool
    {
        // 判断配置是单个 IP 地址还是 CIDR 段
        if (strpos($confIP, '/') === false) {
            // 配置为单个 IP 地址
            return $ip === $confIP;
        } else {
            [$net, $mask] = explode("/", $confIP);

            $ip_net    = ip2long($net);
            $ip_mask   = ~((1 << (32 - $mask)) - 1);
            $ip_ip     = ip2long($ip);
            $ip_ip_net = $ip_ip & $ip_mask;

            return ($ip_ip_net == $ip_net);
        }
    }

}




