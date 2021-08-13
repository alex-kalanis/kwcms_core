<?php

namespace kalanis\kw_modules\Output;


/**
 * Class RawCallback
 * @package kalanis\kw_modules
 * Output callback into Raw data
 */
class RawCallback extends AOutput
{
    /** @var callable */
    protected $callback = null;

    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
    }

    public function output(): string
    {
        return strval(call_user_func($this->callback));
    }
}
