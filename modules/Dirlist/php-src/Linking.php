<?php

namespace KWCMS\modules\Dirlist;


use kalanis\kw_address_handler\Handler;
use kalanis\kw_address_handler\SingleVariable;
use kalanis\kw_address_handler\Sources;
use kalanis\kw_paging\Interfaces\ILink;


/**
 * Class Linking
 * @package kalanis\kw_directory_listing
 * Make links for current page
 */
class Linking implements ILink
{
    const PAGE_KEY = 's';
    protected $handler = null;
    protected $variable = null;

    public function __construct(Sources\Sources $sources, string $key = self::PAGE_KEY)
    {
        $this->handler = new Handler($sources);
        $this->variable = new SingleVariable($this->handler->getParams());
        $this->variable->setVariableName($key);
    }

    public function setPageNumber(int $page): void
    {
        $this->variable->setVariableValue(strval($page));
    }

    public function getPageLink(): string
    {
        return strval($this->handler->getAddress());
    }
}
