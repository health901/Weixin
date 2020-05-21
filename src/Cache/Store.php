<?php


namespace VRobin\Weixin\Cache;


interface Store
{
    public function config($config);

    public function set($key, $value);

    public function get($key, $default = "");
}