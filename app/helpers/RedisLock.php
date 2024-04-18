<?php

namespace app\helpers;
use think\cache\driver\Redis;

class RedisLock
{
    protected $name;
    protected $timeout;

    protected Redis $redis;

    public function __construct($name, $timeout = 30)
    {
        $this->name = $name;
        $this->timeout = $timeout;
        $this->redis = new Redis();
    }

    public function acquire()
    {
        $result = $this->redis->set($this->name, 1, $this->timeout);
        return $result !== false;
    }

    public function release()
    {
        $this->redis->delete($this->name);
    }

    public function filter()
    {
        return $this->redis->get($this->name);
    }
}
