<?php


namespace VRobin\Weixin;

use VRobin\Weixin\Cache\Cache;
use VRobin\Weixin\Request\Apis\TicketGetTicketRequest;

class WeixinJsSignaturePack extends WeixinApi
{
    public function getSignaturePack($url = null)
    {
        $params = array();
        $params['url'] = $url;
        $params['jsapi_ticket'] = $this->createTicket();
        $params['timestamp'] = time();
        $params['noncestr'] = rand(1000, 9999);
        ksort($params);
        $a = array();
        foreach ($params as $k => $v) {
            $a[] = $k . '=' . $v;
        }
        $params['signature'] = sha1(implode("&", $a));
        $params['appid'] = $this->appid;
        return $params;
    }

    protected function createTicket(){
        $cache = Cache::get('jsapi_ticket');
        if ($cache && $cache['expire'] > time()) {
            return $cache['ticket'];
        }
        $data = $this->call(new TicketGetTicketRequest());
        $ticket = $data['ticket'];
        $expire = time() + $data['expires_in'] - 200;
        Cache::set('jsapi_ticket', ['expire' => $expire, 'ticket' => $ticket]);
        return $ticket;
    }
}