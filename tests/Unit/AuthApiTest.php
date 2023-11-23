<?php

namespace Tests\Unit;

use Douyuxingchen\PhpSysAuth\Auth\AuthApi;
use Douyuxingchen\PhpSysAuth\Auth\AuthKey;
use PHPUnit\Framework\TestCase;

class AuthApiTest extends TestCase
{

    // 测试token生成
    public function testTokenGen()
    {
        $token = (new AuthApi('your_app_key'))->token('your_app_secret', ['exp'=>86400]);
        var_dump($token);
        $this->assertTrue(true);
    }


    public function testAppFlush()
    {
        (new AuthApi('your_app_key'))->flushCache();
        $this->assertTrue(true);
    }

}