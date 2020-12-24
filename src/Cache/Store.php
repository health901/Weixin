<?php


namespace VRobin\Weixin\Cache;


interface Store
{
    public function config($config);

    public function set($key, $value, $ttl);

    public function get($key, $default = "");

    public function clear();
}