<?php
namespace Douyuxingchen\PhpSysAuth\Services;

use Douyuxingchen\PhpSysAuth\Caches\BaseCache;
use Douyuxingchen\PhpSysAuth\Enums\ErrCodeEnums;
use Douyuxingchen\PhpSysAuth\Exceptions\TokenInvalidException;
use Douyuxingchen\PhpSysAuth\Exceptions\ValidationException;

class JWTHandler {

    private $secret;

    public function __construct($secret) {
        $this->secret = $secret;
    }

    /**
     * 签名生成
     *
     * @throws ValidationException
     */
    public function generateToken(array $payload) {
        if(!isset($payload['app_key'])) {
            throw new ValidationException('Payload did not find app_key');
        }
        if(!isset($payload['exp'])) {
            throw new ValidationException('Payload did not find exp');
        }
        if((int)$payload['exp'] > 86400) {
            throw new ValidationException('The expiration time cannot exceed 24 hours');
        }

        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->secret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * 签名验证
     *
     * @throws ValidationException|TokenInvalidException
     */
    public function verifyToken($appKey, $token) {
        $tokenParts = explode('.', $token);
        if (count($tokenParts) !== 3) {
            throw new ValidationException('Token format incorrect', ErrCodeEnums::ERR_TOKEN_DELETION);
        }
        $receivedSignature = $tokenParts[2];
        $payload = $tokenParts[0] . '.' . $tokenParts[1];
        $calculatedSignature = $this->base64UrlEncode(hash_hmac('sha256', $payload, $this->secret, true));
        if ($calculatedSignature != $receivedSignature) {
            throw new TokenInvalidException('Token verification failed', ErrCodeEnums::ERR_TOKEN_FAILED);
        }
        $payloadData = json_decode($this->base64UrlDecode($tokenParts[1]), true);

        if(!isset($payloadData['app_key'])) {
            throw new ValidationException('Payload did not find app_key', ErrCodeEnums::ERR_TOKEN_APP_KEY_EMPTY);
        }
        if(!isset($payloadData['exp'])) {
            throw new ValidationException('Payload did not find exp', ErrCodeEnums::ERR_TOKEN_EXP_EMPTY);
        }
        if($appKey != $payloadData['app_key']) {
            throw new ValidationException('The passed appKey and encrypted appKey data are inconsistent', ErrCodeEnums::ERR_TOKEN_APP_KEY_NOT_MATCH);
        }
        if((int)$payloadData['exp'] > 86400) {
            throw new ValidationException('The expiration time cannot exceed 24 hours', ErrCodeEnums::ERR_TOKEN_EXP_NOT_24_HOUR);
        }
        $this->verifyTokenExpire($appKey, $calculatedSignature, (int)$payloadData['exp']);
        return $payloadData;
    }

    /**
     * 如果客户端重写生成key则老的key失效
     *
     * @param $appKey
     * @param $signature
     * @param $exp
     * @return void
     * @throws TokenInvalidException
     */
    private function verifyTokenExpire($appKey, $signature, $exp): void
    {
        $cache = BaseCache::getInstance();

        $authCacheKey = sprintf('auth:app:%s', $appKey);
        $signCacheKey = sprintf('auth:app_sign:%s', $signature);
        if(!$cache->exists($authCacheKey)) {
            $cache->set($authCacheKey, $signature, 'EX', $exp);
            $cache->set($signCacheKey, $appKey, 'EX', $exp);
        }

        $cacheSign = $cache->get($authCacheKey);
        if($cacheSign == $signature) {
            return;
        }

        if($cache->exists($signCacheKey)) {
            throw new TokenInvalidException('Token verification expire', ErrCodeEnums::ERR_TOKEN_EXP);
        }

        throw new TokenInvalidException('Token verification failed', ErrCodeEnums::ERR_TOKEN_FAILED);
    }

    private function base64UrlEncode($data): bool|string
    {
        $b64 = base64_encode($data);
        if ($b64 === false) {
            return false;
        }
        $url = strtr($b64, '+/', '-_');
        return rtrim($url, '=');
    }

    private function base64UrlDecode($data): bool|string
    {
        $b64 = strtr($data, '-_', '+/');
        $decoded = base64_decode($b64, true);
        if ($decoded === false) {
            return false;
        }
        return $decoded;
    }
}