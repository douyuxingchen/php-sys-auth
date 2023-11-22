<?php
namespace Douyuxingchen\PhpSysAuth\Auth;

use Douyuxingchen\PhpSysAuth\Enums\ErrCodeEnums;
use Douyuxingchen\PhpSysAuth\Exceptions\ErrCodeException;
use Douyuxingchen\PhpSysAuth\Exceptions\TokenInvalidException;
use Douyuxingchen\PhpSysAuth\Exceptions\ValidationException;
use Douyuxingchen\PhpSysAuth\Models\SysAuthApp;
use Douyuxingchen\PhpSysAuth\Services\AppRouteService;
use Douyuxingchen\PhpSysAuth\Services\AppService;
use Douyuxingchen\PhpSysAuth\Services\IPService;
use Douyuxingchen\PhpSysAuth\Services\JWTHandler;
use Douyuxingchen\PhpSysAuth\Services\RateLimiterService;
use Illuminate\Support\Facades\Config;

class AuthApi
{
    private $appKey;
    private $token;

    private $isIPLimit;
    private $isApiRate;
    private $isRouteLimit;

    /**
     * @param string $appKey 应用key
     */
    public function __construct(string $appKey) {
        $this->appKey = $appKey;

        $this->isIPLimit = Config::get('sys_auth.ip_limit');
        $this->isApiRate = Config::get('sys_auth.api_rate');
        $this->isRouteLimit = Config::get('sys_auth.route_limit');
    }

    /**
     * @param string $token 请求token
     * @return $this
     */
    public function setToken(string $token) : AuthApi
    {
        $this->token = $token;
        return $this;
    }

    /**
     * 清理缓存
     *
     * @return array
     */
    public function flushCache()
    {
        return [
            (new AppService())->delCache($this->appKey),
            (new AppRouteService())->delCache($this->appKey),
        ];
    }

    /**
     * 接口安全验证器
     *
     * @return void
     * @throws TokenInvalidException
     * @throws ValidationException
     * @throws ErrCodeException
     */
    public function verify()
    {
        $app = (new AppService())->createApp($this->appKey);

        // appKey是否启用
        if($app->getStatus() != SysAuthApp::STATUS_APPROVED) {
            throw new ErrCodeException('The approval status of changing the appKey is not open', ErrCodeEnums::ERR_APP_STATUS_NOT_OPEN);
        }

        $jwt = new JWTHandler($app->getSecretKey());
        $payload = $jwt->verifyToken($this->appKey, $this->token);
        $exp = (int)$payload['exp'];

        $this->IPLimit($app); // 黑白ip限流
        $this->ApiRate($app);  // 频率限流
        $this->RouteLimit($app, $exp); // 接口白名单限流
    }

    /**
     * @throws ErrCodeException
     */
    private function IPLimit(AppService $app)
    {
        if(!$this->isIPLimit) {
            return;
        }

        $ip = IPService::getClientIP();

        // 白名单模式
        if($app->getIpLimitType() == SysAuthApp::IP_WHITE) {
            $ipList = explode(',', $app->getIpWhiteList());
            if(empty($ipList)) {
                throw new ErrCodeException('In IP whitelist mode, the IP is blocked', ErrCodeEnums::ERR_IP_WHITE);
            }

            foreach ($ipList as $confIP) {
                if(IPService::validateIP($ip, $confIP)) {
                    return;
                }
            }
            throw new ErrCodeException('In IP whitelist mode, the IP is blocked', ErrCodeEnums::ERR_IP_WHITE);
        }

        // 黑名单模式
        if($app->getIpLimitType() == SysAuthApp::IP_BLACK) {
            $ipList = explode(',', $app->getIpBlackList());
            if(empty($ipList)) {
                return;
            }
            foreach ($ipList as $confIP) {
                if(IPService::validateIP($ip, $confIP)) {
                    throw new ErrCodeException('In IP blacklist mode, the IP is blocked', ErrCodeEnums::ERR_IP_BLACK);
                }
            }
        }
    }

    /**
     * @param AppService $app
     * @return void
     * @throws ErrCodeException
     */
    private function ApiRate(AppService $app)
    {
        if(!$this->isApiRate) {
            return;
        }

        (new RateLimiterService($app->getApiLimit()))->throttle($app->getAppKey());
    }

    /**
     * @throws ErrCodeException
     */
    private function RouteLimit(AppService $app, $exp)
    {
        if(!$this->isRouteLimit) {
            return;
        }

        $uri = request()->route()->uri;
        $route = (new AppRouteService())->getRouteList($app->getAppID(), $app->getAppKey(), $exp);
        if(!in_array(strtolower($uri), $route)) {
            throw new ErrCodeException('The interface you requested is not authorized', ErrCodeEnums::ERR_URI_UNAUTHORIZED);
        }
    }
}