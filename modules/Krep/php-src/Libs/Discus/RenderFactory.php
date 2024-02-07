<?php

namespace KWCMS\modules\Krep\Libs\Discus;


use kalanis\kw_forums\Interfaces\ITargets;
use KWCMS\modules\Krep\Libs\Interfaces\IContent;
use KWCMS\modules\Krep\Libs\Shared\PageData;


/**
 * Class RenderFactory
 * @package KWCMS\modules\Krep\Libs\Discus
 */
class RenderFactory
{
    /** @var RenderTopics */
    protected $topics = null;
    /** @var RenderThemas */
    protected $themas = null;

    public function __construct(RenderSinglePost $post, RenderTopics $topics, RenderThemas $themas)
    {
        $this->post = $post;
        $this->topics = $topics;
        $this->themas = $themas;
    }

    public function whichContent(PageData $pageData): IContent
    {
        if ($pageData->currentPost && ITargets::LISTING_TOPIC == $pageData->getListingType()) {
            return $this->post;
        } elseif (ITargets::LISTING_TOPIC == $pageData->getListingType()) {
            return $this->topics;
        } else {
            return $this->themas;
        }
    }
}
