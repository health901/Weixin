<?php


namespace VRobin\Weixin\Request;


use VRobin\Weixin\Exception\ApiException;
use VRobin\Weixin\Request\Http\Request;

trait ApiTrait
{
    /**
     *
     * @param string $url
     * @param array $data
     * @param string $method
     * @param bool $raw
     * @return string
     * @throws ApiException
     */
    public function request($url, $data = array(), $method = 'get', $raw = false)
    {
        if ($method == 'post') {
            $result = $this->post($url, $data);
        } else {
            $result = $this->get($url, $data);
        }
        $data = json_decode($result, true);
        if (isset($data['errcode']) && $data['errcode']) {
            throw new ApiException($data['errcode']. ':' .$data['errmsg'], $data['errcode']);
        }
        return $raw ? $result : $data;
    }

    protected function get($api, $data)
    {
        $url = $this->apiUrl($api);
        return Request::get($url, $data);
    }

    protected function post($api, $data)
    {
        $url = $this->apiUrl($api);
        return Request::post($url, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    protected function apiUrl($api)
    {
        $url = $this->apiUrl . $api;
        if ($this->accessToken) {
            $url .= '?access_token=' . $this->accessToken;
        }

        return $url;
    }
}