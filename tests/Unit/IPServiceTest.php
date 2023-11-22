<?php
namespace Tests\Unit;

use Douyuxingchen\PhpSysAuth\Services\IPService;
use PHPUnit\Framework\TestCase;

class IPServiceTest extends TestCase
{
    // 所有IPv4地址
    const CONF_IP_All = '0.0.0.0/0';
    // 指定单个IP
    const CONF_IP = '192.168.1.1';
    // 指定CIDR段
    const CONF_IP_CIDR = '192.168.1.0/24';


    public function testAllIP()
    {
        $res = IPService::validateIP('172.67.43.1', self::CONF_IP_All);
        $this->assertTrue($res);
    }

    public function testIP()
    {
        $res = IPService::validateIP('192.168.1.1', self::CONF_IP);
        $this->assertTrue($res);
    }

    public function testIPFailed()
    {
        $res = IPService::validateIP('192.168.1.12', self::CONF_IP);
        $this->assertFalse($res);
    }

    public function testIPCidr()
    {
        $res = IPService::validateIP('192.168.1.100', self::CONF_IP_CIDR);
        $this->assertTrue($res);
    }

    public function testIPCidrFailed()
    {
        $res = IPService::validateIP('43.10.1.100', self::CONF_IP_CIDR);
        $this->assertFalse($res);
    }

}