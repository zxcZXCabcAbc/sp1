<?php

namespace app\helpers;

use think\facade\Cache;

class RedisHelper
{
    public  static $redis;

    public static function redis()
    {
        return Cache::store('redis');
    }

    public static function Get(string $key)
    {
        return self::redis()->get($key);
    }

    public static function Set(string $key,mixed $val,int $expire = null)
    {

        return self::redis()->set($key,$val,$expire);
    }

    public static function Del(string $key)
    {
        return self::redis()->delete($key);
    }

    public static function Exists(string $key)
    {
        return self::redis()->has($key);
    }


}