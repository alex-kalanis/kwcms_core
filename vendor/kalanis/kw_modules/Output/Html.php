<?php

namespace kalanis\kw_modules\Output;


/**
 * Class Html
 * @package kalanis\kw_modules
 * Output into Html data string
 * It's extra because it's necessary for wrapping with other modules - other outputs cannot do this
 */
class Html extends AOutput
{
    protected bool $canWrap = true;
    protected string $content = '';

    public function setContent(string $content = ''): self
    {
        $this->content = $content;
        return $this;
    }

    public function output(): string
    {
        return $this->content;
    }
}
