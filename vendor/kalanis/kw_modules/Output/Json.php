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
        header("Content-Type: application/json");
        return json_encode($this->content);
    }
}
