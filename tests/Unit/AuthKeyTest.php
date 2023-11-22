<?php

namespace Tests\Unit;

use Douyuxingchen\PhpSysAuth\Auth\AuthKey;
use PHPUnit\Framework\TestCase;

class AuthKeyTest extends TestCase
{

    // 测试appkey生成
    public function testAppKey()
    {
        $str = AuthKey::genAppKey();
        var_dump($str, strlen($str));
        $this->assertEquals(strlen($str), 16);
    }

    // 测试appsecret生成
    public function testAppSecret()
    {
        $str = AuthKey::genAppSecret();
        var_dump($str);
        $this->assertEquals(strlen($str), 32);
    }

}