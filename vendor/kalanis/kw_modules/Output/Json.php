<?php

namespace kalanis\kw_modules\Output;


/**
 * Class Json
 * @package kalanis\kw_modules
 * Output into Json
 */
class Json extends AOutput
{
    /** @var array<string|int, string|int|float|array<string|int|float|array<string|int|float>>> */
    protected $content = null;

    /**
     * @param array<string|int, string|int|float|array<string|int|float|array<string|int|float>>> $contentToEncode
     * @return $this
     */
    public function setContent($contentToEncode): self
    {
        $this->content = $contentToEncode;
        return $this;
    }

    public function output(): string
    {
        if (!headers_sent()) {
            // @codeCoverageIgnoreStart
            header('Content-Type: application/json');
        }
        // @codeCoverageIgnoreEnd
        return strval(json_encode($this->content));
    }
}
