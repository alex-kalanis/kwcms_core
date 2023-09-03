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
    protected $canWrap = true;
    /** @var string */
    protected $content = '';

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
