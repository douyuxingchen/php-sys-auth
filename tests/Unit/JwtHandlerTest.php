<?php

namespace Tests\Unit;

use Douyuxingchen\PhpSysAuth\Services\JWTHandler;
use PHPUnit\Framework\TestCase;

class JwtHandlerTest extends TestCase
{

    /**
     * 测试签名生成
     *
     * @return void
     * @throws \Douyuxingchen\PhpSysAuth\Exceptions\ValidationException
     */
    public function testGenerateToken()
    {
        $appSecret = 'test_secret';
        $payload = [
            'app_key' => 'testapp',
            'exp' => 500
        ];
        $jwt = new JWTHandler($appSecret);
        $token = $jwt->generateToken($payload);
        var_dump($token);

        $this->assertTrue(true);
    }

    /**
     * 测试签名验证
     *
     * @return void
     * @throws \Douyuxingchen\PhpSysAuth\Exceptions\TokenInvalidException
     * @throws \Douyuxingchen\PhpSysAuth\Exceptions\ValidationException
     */
    public function testVerifyToken()
    {
         $appSecret = 'test_secret';
        $payload = [
            'app_key' => 'testapp',
            'exp' => 500
        ];
        $jwt = new JWTHandler($appSecret);
        $token = $jwt->generateToken($payload);

        $data = $jwt->verifyToken($token);
        var_dump($data);

        $this->assertEquals($data['app_key'], 'testapp');
    }

}