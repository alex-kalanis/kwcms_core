<?php

namespace kalanis\kw_table_form_kw\Fields;


use kalanis\kw_connect\Interfaces\IFilterFactory;
use kalanis\kw_table\Interfaces\Table\IFilterRender;


/**
 * Class InputCallback
 * @package kalanis\kw_table_form_kw\Fields
 * Also - put inside filter what you want
 */
class InputCallback extends AField implements IFilterRender
{
    protected $callback = null;

    public function __construct(callable $callback, array $attributes = [])
    {
        $this->setCallback($callback);
        parent::__construct($attributes);
    }

    public function setCallback(callable $callback): void
    {
        $this->callback = $callback;
    }

    protected function getFilterAction(): string
    {
        return IFilterFactory::ACTION_EXACT;
    }

    public function add(): void
    {
    }

    public function renderContent(): string
    {
        return call_user_func($this->callback, $this->attributes);
    }
}
