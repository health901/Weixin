<?php


namespace VRobin\Weixin\Apis\Mp;


use VRobin\Weixin\Apis\ApiRequest;

/**
 * 修改永久图文素材
 * Class MaterialUpdateNewsApiRequest
 * @package VRobin\Weixin\ApiRequest\Apis
 */
class MaterialUpdateNewsApiRequest extends ApiRequest
{
    protected $api = 'material/update_news';

    protected $method = 'POST';

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