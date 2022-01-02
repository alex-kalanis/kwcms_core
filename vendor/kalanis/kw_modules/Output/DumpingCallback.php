<?php

namespace kalanis\kw_modules\Output;


/**
 * Class DumpingCallback
 * @package kalanis\kw_modules
 * Output callback dumps itself directly and I want it in raw data string
 */
class DumpingCallback extends AOutput
{
    /** @var callable */
    protected $callback = null;

    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }

    public function output(): string
    {
        ob_start();
        $content = call_user_func($this->callback);
        $content .= ob_get_clean();
        return strval($content);
    }
}
