<?php

namespace kalanis\kw_modules\Output;


/**
 * Class Json
 * @package kalanis\kw_modules
 * Output into Json
 */
class Json extends AOutput
{
    protected $content = null;

    public function setContent($contentToEncode)
    {
        $this->content = $contentToEncode;
        return $this;
    }

    public function output(): string
    {
        return json_encode($this->content);
    }
}
