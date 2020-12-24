<?php


namespace VRobin\Weixin\Apis\Mp;


use VRobin\Weixin\Apis\ApiRequest;

/**
 * 新增图文
 * Class MaterialAddNewsApiRequest
 * @package VRobin\Weixin\ApiRequest\Apis
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Adding_Permanent_Assets.html
 */
class MaterialAddNewsApiRequest extends ApiRequest
{
    protected $api = 'cgi-bin/material/add_news';

    protected $method = 'POST';

    public function setArticles($articles)
    {
        $this->data['articles'] = $articles;
    }
}