<?php


namespace VRobin\Weixin\Cache;


class File implements Store
{
    protected $cacheDir;
    protected $cacheFile;


    /**
     * @param $key
     * @param string $default
     * @return false|mixed
     */
    public function get($key, $default = "")
    {
        $cachefile = $this->getCacheFile();
        if (file_exists($cachefile)) {
            $_cache = file_get_contents($cachefile);
            if ($_cache && $cache = unserialize($_cache)) {
                if ($cache['expire'] < time()) {
                    return $cache['cache'][$key];
                }
            }
        }
        return false;
    }

    public function set($key, $value, $ttl)
    {
        $cacheFile = $this->getCacheFile();
        $cache = array($key => $value);
        $expire = time() + $ttl;
        file_put_contents($cacheFile, serialize(['cache' => $cache, 'expire' => $expire]));
    }

    public function clear()
    {
        $cacheFile = $this->getCacheFile();
        if ($cacheFile) {
            unlink($cacheFile);
        }
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