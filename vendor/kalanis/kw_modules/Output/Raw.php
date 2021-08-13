<?php

namespace kalanis\kw_modules\Output;


/**
 * Class Raw
 * @package kalanis\kw_modules
 * Output into Raw data string
 */
class Raw extends AOutput
{
    protected $content = null;

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function output(): string
    {
        return (string)$this->content;
    }
}
