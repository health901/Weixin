<?php


namespace VRobin\Weixin;

use VRobin\Weixin\Apis\MpApi;
use VRobin\Weixin\Cache\Cache;
use VRobin\Weixin\Apis\Mp\TicketGetTicketApiRequest;

class WeixinJsSignaturePack extends MpApi
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

    protected function createTicket()
    {
        $ticketKey = 'jsapi_ticket_' . $this->appid;
        $cache = Cache::get($ticketKey);
        if ($cache) {
            return $cache;
        }
        $data = $this->call(new TicketGetTicketApiRequest());
        $ticket = $data['ticket'];
        $ttl = $data['expires_in'] - 200;
        Cache::set($ticketKey, $ticket, $ttl);
        return $ticket;
    }
}