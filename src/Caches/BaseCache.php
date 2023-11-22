<?php
namespace Douyuxingchen\PhpSysAuth\Caches;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;

class BaseCache
{
    private static $instance;

    private $redis;

    private function __construct() {
        $connect = Config::get('sys_auth.redis_connect_name');
        $this->redis = Redis::connection($connect);
    }

    public static function getInstance(): BaseCache
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->redis, $method], $arguments);
    }
}