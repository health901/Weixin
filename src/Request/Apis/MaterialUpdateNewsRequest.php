<?php


namespace VRobin\Weixin\Request\Apis;


use VRobin\Weixin\Request\Request;

/**
 * 修改永久图文素材
 * Class MaterialUpdateNewsRequest
 * @package VRobin\Weixin\Request\Apis
 */
class MaterialUpdateNewsRequest extends Request
{
    protected $api = 'material/update_news';

    public function setIndex($index)
    {
        $this->data['index'] = $index;
    }

    public function setMediaId($media_id)
    {
        $this->data['media_id'] = $media_id;
    }


    public function setArticles($articles)
    {
        $this->data['articles'] = $articles;
    }
}