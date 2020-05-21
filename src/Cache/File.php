<?php


namespace VRobin\Weixin\Cache;


class File implements Store
{
    protected $cacheDir;
    protected $cacheFile;


    public function get($key, $default = "")
    {
        $cachefile = $this->getCacheFile();
        if (file_exists($cachefile)) {
            $_cache = file_get_contents($cachefile);
            if ($_cache && $cache = unserialize($_cache)) {
                return $cache[$key];
            }
        }
        return false;
    }

    public function set($key, $value)
    {
        $cachefile = $this->getCacheFile();
        $cache = array($key => $value);
        file_put_contents($cachefile, serialize($cache));
    }

    public function config($config)
    {
        $this->cacheDir = $config['cacheDir'] ?? '';
        $this->cacheFile = $config['cacheFile'] ?? 'weixin.cache';
    }

    protected function getCacheFile()
    {
        return $this->cacheDir . '/' . $this->cacheFile;
    }
}