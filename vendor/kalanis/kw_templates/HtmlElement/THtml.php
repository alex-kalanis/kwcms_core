<?php

namespace kalanis\kw_templates\HtmlElement;


/**
 * Trait THtml
 * @package kalanis\kw_templates\Template
 * Trait for describe internal content of element, usually HTML code
 * Extend child of AHtmlElement
 * @author Adam Dornak original
 * @author Petr Plsek refactored
 */
trait THtml
{
    protected $innerHtml = '';

    /**
     * Set internal content of element
     * @param string $value
     * @return $this
     */
    public final function addInnerHTML(string $value): self
    {
        $this->innerHtml = $value;
        return $this;
    }

    /**
     * Get internal content of element
     * @return string
     */
    public final function getInnerHTML(): string
    {
        return $this->innerHtml;
    }
}
