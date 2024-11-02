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
    public function __construct(
        protected readonly RenderSinglePost $post,
        protected readonly RenderTopics $topics,
        protected readonly RenderThemas $themas,
    )
    {
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
