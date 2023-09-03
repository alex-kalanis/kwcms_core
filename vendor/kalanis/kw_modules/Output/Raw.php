<?php

namespace kalanis\kw_modules\Output;


/**
 * Class Raw
 * @package kalanis\kw_modules
 * Output into Raw data string
 */
class Raw extends AOutput
{
    /** @var mixed */
    protected $content = null;

    /**
     * @param mixed $content
     * @return $this
     */
    public function setContent($content): self
    {
        $this->content = $content;
        return $this;
    }

    public function output(): string
    {
        return strval($this->content);
    }
}
