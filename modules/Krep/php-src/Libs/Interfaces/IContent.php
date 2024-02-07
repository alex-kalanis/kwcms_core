<?php

namespace KWCMS\modules\Krep\Libs\Interfaces;


use KWCMS\modules\Krep\Libs\Shared\PageData;


/**
 * Interface IContent
 * @package KWCMS\modules\Krep\Libs\Interfaces
 */
interface IContent
{
    public function getContent(PageData $pageData): string;
}
