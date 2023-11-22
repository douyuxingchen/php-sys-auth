<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class RedisTest extends TestCase
{

    public function testRedisSet()
    {
        // 使用 Redis 服务
        $redis = app('redis')->connection('redis_connection_name');


    }

}