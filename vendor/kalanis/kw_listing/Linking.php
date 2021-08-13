<?php

namespace kalanis\kw_listing;


use kalanis\kw_address_handler\Handler;
use kalanis\kw_address_handler\SingleVariable;
use kalanis\kw_address_handler\Sources;
use kalanis\kw_input\Interfaces\IVariables;
use kalanis\kw_paging\Interfaces\ILink;


/**
 * Class Linking
 * @package kalanis\kw_listing
 * Make links for current page
 */
class Linking implements ILink
{
    const PAGE_KEY = 's';
    protected $handler = null;
    protected $variable = null;

    public function __construct(IVariables $inputs)
    {
        $this->handler = new Handler(new Sources\Inputs($inputs));
        $this->variable = new SingleVariable($this->handler->getParams());
        $this->variable->setVariableName(static::PAGE_KEY);
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
