<?php


namespace VRobin\Weixin\Request\Http;


class Request
{
    /**
     *
     * @param string $url
     * @param array $params
     * @param array $option
     * @return string
     */
    public static function get($url, $params = array(), $option = array())
    {

        if (!empty($params)) {
            $p = http_build_query($params);

            if (FALSE === strpos($url, '?')) {
                $url = $url . '?' . $p;
            } else {
                $url = $url . '&' . $p;
            }
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt_array($ch, $option);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result ? $result : false;
    }


    /**
     *
     * @param string $url
     * @param mixed $params
     * @param array $option
     * @return string
     */
    public static function post($url, $params = NULL, $option = array())
    {
        if (is_array($params) && !empty($params)) {
            foreach ($params as $k => $v) {
                if (stripos($k, '@') === 0) {
                    unset($params[$k]);
                    $file = new \CURLFile($v);
                    $params[substr($k, 1)] = $file;
                }
            }

        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt_array($ch, $option);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result ? $result : false;
    }


}