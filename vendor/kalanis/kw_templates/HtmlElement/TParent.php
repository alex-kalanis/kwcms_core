<?php

namespace kalanis\kw_templates\HtmlElement;


/**
 * Trait TParent
 * @package kalanis\kw_templates\Template
 * Trait for work with parenting of html elements
 * Extend child of AHtmlElement
 * @author Adam Dornak original
 * @author Petr Plsek refactored
 */
trait TParent
{
    /** @var IHtmlElement|null */
    protected $parent;

    /**
     * Set parent element
     * @param IHtmlElement|null $parent
     * @return $this
     */
    public final function setParent(?IHtmlElement $parent = null): self
    {
        $this->parent = $parent;
        $this->afterParentSet();
        return $this;
    }

    /**
     * Returns parent element
     * @return IHtmlElement|null
     */
    public final function getParent(): ?IHtmlElement
    {
        return $this->parent;
    }

    /**
     * Change element settings after new parent has been set
     */
    protected function afterParentSet()
    {
    }

    /**
     * Add $element after current one - if there is any parent
     * @param THtmlElement|string $element
     * @param string $alias
     * @return $this
     */
    public final function append($element, ?string $alias = null): self
    {
        if ($this->parent instanceof IHtmlElement) {
            $this->parent->addChild($element, $alias);
        }
        return $this;
    }
}
