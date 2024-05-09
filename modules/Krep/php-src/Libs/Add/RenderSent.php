<?php

namespace KWCMS\modules\Krep\Libs\Add;


use KWCMS\modules\Krep\Libs;


class RenderSent implements Libs\Interfaces\IContent
{
    protected Libs\Shared\Links $links;

    public function __construct(Libs\Shared\Links $links)
    {
        $this->links = $links;
    }

    public function getContent(Libs\Shared\PageData $pageData): string
    {
        $b = new Libs\Template("sent");
        $b->change('{HIGH}', __("post_sent"));
        $b->change('{DESC}', __("post_sent_dsc"));
        $b->change('{CONTINUE}', $this->links->get($pageData, true));
        return $b->render();
    }
}
