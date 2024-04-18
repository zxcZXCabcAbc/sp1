<?php

namespace app\helpers;

use think\cache\driver\Redis;

class RedisHelper
{
    public static Redis $redis;
    public function __construct(){
        self::$redis = new Redis();
    }

    public static function Get(string $key)
    {
        return self::$redis->get($key);
    }

    public static function Set(string $key,mixed $val,int $expire = null)
    {
        return self::$redis->set($key,$val,$expire);
    }

    public static function Del(string $key)
    {
        return self::$redis->delete($key);
    }

    public static function Exists(string $key)
    {
        return self::$redis->has($key);
    }


}