<?php

namespace kalanis\kw_modules\Output;


/**
 * Class Json
 * @package kalanis\kw_modules
 * Error Output into Json
 */
class JsonError extends AOutput
{
    protected $content = null;

    public function setContent($code, string $message)
    {
        $this->content = compact('code', 'message');
        return $this;
    }

    public function output(): string
    {
        return json_encode($this->content);
    }
}
