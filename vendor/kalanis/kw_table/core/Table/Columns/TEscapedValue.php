<?php

namespace kalanis\kw_table\core\Table\Columns;


/**
 * Trait TEscapedValue
 * @package kalanis\kw_table\core\Table\Columns
 * Replace value selection from source, add content escape
 * Can be disabled due setting flag on null
 * Due XSS, nice for strings
 */
trait TEscapedValue
{
    protected $flags = null;

    /**
     * @param int|null $flags example: ENT_NOQUOTES | ENT_HTML5
     * @link http://php.net/manual/en/function.htmlspecialchars.php
     */
    public function setEscapeFlags(?int $flags): void
    {
        $this->flags = $flags;
    }

    protected function valueEscape($content)
    {
        return (is_null($this->flags))
            ? $content
            : htmlspecialchars($content, $this->flags, 'UTF-8', false);
    }
}