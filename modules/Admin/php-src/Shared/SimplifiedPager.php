<?php

namespace KWCMS\modules\Admin\Shared;


use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_paging\Interfaces\ILink;
use kalanis\kw_paging\Interfaces\IPositions;
use KWCMS\modules\Admin\Templates\PagerTemplate;


/**
 * Class SimplifiedPager
 * @package KWCMS\modules\Admin\Shared
 * This output can be filled inside the structure
 */
class SimplifiedPager extends \kalanis\kw_paging\Render\SimplifiedPager
{
    /**
     * @param IPositions $positions
     * @param ILink $link
     * @param int $displayPages
     * @throws LangException
     */
    public function __construct(IPositions $positions, ILink $link, int $displayPages = IPositions::DEFAULT_DISPLAY_PAGES_COUNT)
    {
        Lang::load('Admin');
        parent::__construct($positions, $link, $displayPages);
        $this->pager = new PagerTemplate();
        $this->pager->setLang(new PagerLang());
    }
}
