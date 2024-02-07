<?php

namespace KWCMS\modules\Krep\Libs\Add;


use KWCMS\modules\Krep\Libs;


/**
 * Class BlockResult
 * @package KWCMS\modules\Krep\Libs\Add
 */
class BlockResult
{
    public function render(Libs\Interfaces\IContent $content, Libs\Shared\PageData $pageData): string
    {
        return $content->getContent($pageData);
    }
}
