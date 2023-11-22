<?php

namespace Tests\Unit;

use Douyuxingchen\PhpSysAuth\Auth\AuthKey;
use PHPUnit\Framework\TestCase;

class AuthKeyTest extends TestCase
{

    // 测试appkey生成
    public function testAppKey()
    {
        $appKey = AuthKey::genAppKey();
        var_dump($appKey);
        $this->assertTrue(true);
    }

    // 测试appsecret生成
    public function testAppSecret()
    {
        $appKey = AuthKey::genAppSecret();
        var_dump($appKey);

        $this->assertTrue(true);
    }

}