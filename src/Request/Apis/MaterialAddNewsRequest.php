<?php


namespace VRobin\Weixin\Request\Apis;


use VRobin\Weixin\Request\Request;

/**
 * 新增图文
 * Class MaterialAddNewsRequest
 * @package VRobin\Weixin\Request\Apis
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Adding_Permanent_Assets.html
 */
class MaterialAddNewsRequest extends Request
{
    protected $api = 'material/add_news';


    public function setArticles($articles)
    {
        $this->data['articles'] = $articles;
    }
}