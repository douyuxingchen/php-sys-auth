<?php
namespace Douyuxingchen\PhpSysAuth\Auth;

use Douyuxingchen\PhpSysAuth\Enums\ErrCodeEnums;
use Douyuxingchen\PhpSysAuth\Exceptions\ConfigException;
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
    private $tokenCache = true;

    /**
     * @param string $appKey 应用key
     */
    public function __construct(string $appKey) {
        $this->appKey = $appKey;
    }

    /**
     * 设置Token
     *
     * @param string $token 请求token
     * @return $this
     */
    public function setToken(string $token) : AuthApi
    {
        $this->token = $token;
        return $this;
    }

    /**
     * 是否使用Token缓存
     *
     * @param bool $status
     * @return $this
     */
    public function setTokenCache(bool $status) : AuthApi
    {
        $this->tokenCache = $status;
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
     * 接口Token生成
     *
     * @param string $appSecret
     * @param array $payload
     * @return string
     * @throws ValidationException
     */
    public function token(string $appSecret, array $payload)
    {
        $jwt = new JWTHandler($appSecret);
        return $jwt->generateToken($payload);
    }

    /**
     * 接口安全验证
     *
     * @return void
     * @throws TokenInvalidException
     * @throws ValidationException
     * @throws ErrCodeException
     * @throws ConfigException
     */
    public function verify()
    {
        $app = (new AppService())->createApp($this->appKey);

        // appKey是否启用
        if($app->getStatus() != SysAuthApp::STATUS_APPROVED) {
            throw new ErrCodeException('The approval status of changing the appKey is not open', ErrCodeEnums::ERR_APP_STATUS_NOT_OPEN);
        }

        $jwt = new JWTHandler($app->getSecretKey());
        $jwt->verifyToken($this->appKey, $this->token, $this->tokenCache);

        $this->IPLimit($app); // 黑白ip限流
        $this->ApiRate($app);  // 频率限流
        $this->RouteLimit($app); // 接口白名单限流
    }

    /**
     * @throws ErrCodeException
     */
    private function IPLimit(AppService $app)
    {
        $status = Config::get('sys_auth.ip_limit.status');
        $whiteList = Config::get('sys_auth.ip_limit.white_list');

        if(!$status) {
            return;
        }

        $ip = IPService::getClientIP();

        if(!empty($whiteList) && in_array($ip, $whiteList)) {
            return;
        }

        // 白名单模式
        if($app->getIpLimitType() == SysAuthApp::IP_WHITE) {
            $ipList = explode(',', $app->getIpWhiteList());
            if(empty($ipList)) {
                throw new ErrCodeException(sprintf('In IP whitelist mode, the IP is blocked [%s]', $ip), ErrCodeEnums::ERR_IP_WHITE);
            }

            foreach ($ipList as $confIP) {
                if(IPService::validateIP($ip, $confIP)) {
                    return;
                }
            }
            throw new ErrCodeException(sprintf('In IP whitelist mode, the IP is blocked [%s]', $ip), ErrCodeEnums::ERR_IP_WHITE);
        }

        // 黑名单模式
        if($app->getIpLimitType() == SysAuthApp::IP_BLACK) {
            $ipList = explode(',', $app->getIpBlackList());
            if(empty($ipList)) {
                return;
            }
            foreach ($ipList as $confIP) {
                if(IPService::validateIP($ip, $confIP)) {
                    throw new ErrCodeException(sprintf('In IP blacklist mode, the IP is blocked [%s]', $ip), ErrCodeEnums::ERR_IP_BLACK);
                }
            }
        }
    }

    /**
     * @throws ErrCodeException|ConfigException
     */
    private function ApiRate(AppService $app)
    {
        $status = Config::get('sys_auth.api_rate.status');
        $eval = Config::get('sys_auth.api_rate.eval');
        $rateType = Config::get('sys_auth.api_rate.rate_type');

        if(!$status) {
            return;
        }

        // 配置文件错误
        if(!in_array($rateType, RateLimiterService::RateType)) {
            throw new ConfigException('Error in configuration file [sys_auth.api_rate.rate_type]',
                ErrCodeEnums::ERR_CONF_FAILED);
        }

        $method = $rateType;
        if($eval) {
            $method .= 'Eval';
        }

        if(!method_exists(RateLimiterService::class, $method)) {
            throw new ConfigException('Error in configuration file [sys_auth.api_rate]',
                ErrCodeEnums::ERR_CONF_FAILED);
        }

        (new RateLimiterService($app->getApiLimit()))->$method($app->getAppKey());
    }

    /**
     * @throws ErrCodeException
     */
    private function RouteLimit(AppService $app)
    {
        $status = Config::get('sys_auth.route_limit.status');
        $whiteList = Config::get('sys_auth.route_limit.white_list');

        if(!$status) {
            return;
        }

        $uri = request()->route()->uri;
        if(!empty($whiteList) && in_array($uri, $whiteList)) {
            return;
        }

        $route = (new AppRouteService())->getRouteList($app->getAppID(), $app->getAppKey());
        if(!in_array(strtolower($uri), $route)) {
            throw new ErrCodeException(sprintf('The interface you requested is not authorized [%s]', $uri), ErrCodeEnums::ERR_URI_UNAUTHORIZED);
        }
    }
}