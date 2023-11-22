<?php
namespace Douyuxingchen\PhpSysAuth\Caches;

use Illuminate\Support\Facades\Redis;

class BaseCache
{
    private static $instance;

    private $redis;

    private function __construct() {
         $this->redis = Redis::connection();
    }

    public static function getInstance(): BaseCache
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * key是否存在
     *
     * @param string $key
     * @return mixed
     */
    public function exists(string $key)
    {
         return $this->redis->exists($key);
    }

    /**
     * 设置值
     *
     * @param string $key Redis中的键名
     * @param mixed $val 要存储在Redis中的值
     * @param string $expireResolution （可选）过期时间的解析方式，用于设置精确的过期时间。在Laravel的Redis中，这个参数可以传入一个字符串值，比如EX或者PX，用于指定过期时间的单位（秒或毫秒）
     * @param int $expireTTL （可选）设置键的过期时间。如果$expireResolution参数已经指定了过期时间的单位，这里传入的值就是具体的时间数值。如果没有指定$expireResolution，这里传入的值将被视为秒数。
     * @param string $flag （可选）一些特殊标志，例如NX、XX等，可以影响set操作的行为。比如NX标志表示只有键不存在时才设置值，XX表示只有键已存在时才设置值。
     * @return mixed
     */
    public function set(string $key, $val, $expireResolution = null, $expireTTL = null, $flag = null)
    {
        return $this->redis->set($key, $val, $expireResolution = null, $expireTTL = null, $flag = null);
    }

    /**
     * 获取值
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->redis->get($key);
    }

    /**
     * 给key设置过期时间
     *
     * @param string $key
     * @param int $expireTTL （秒）
     * @return mixed
     */
    public function expire(string $key, int $expireTTL)
    {
        return $this->redis->expire($key, $expireTTL);
    }

    /**
     * 删除key
     *
     * @param string $key
     * @return int
     */
    public function del(string $key) : int
    {
        return (int)$this->redis->del($key);
    }

    /**
     * 获取key的过期时间
     *
     * @param string $key
     * @return int
     */
    public function getTtl(string $key) : int
    {
        return (int)$this->redis->ttl($key);
    }

}