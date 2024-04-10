<?php

namespace KWCMS\modules\Dirlist\Libs;


use kalanis\kw_address_handler\Handler;
use kalanis\kw_address_handler\SingleVariable;
use kalanis\kw_address_handler\Sources;
use kalanis\kw_paging\Interfaces\ILink;


/**
 * Class Linking
 * @package KWCMS\modules\Dirlist\Libs
 * Make links for current page
 */
class Linking implements ILink
{
    public const PAGE_KEY = 's';
    protected Handler $handler;
    protected SingleVariable $variable;

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
