<?php

namespace kalanis\kw_templates\HtmlElement;


use Traversable;


/**
 * Abstraction of HTML element - trait which can be used
 * Each HTML element must have a few following things
 * 1. must be able to render self
 * 2. must can tell what children have
 *    it's possible to have 0 - n children
 * 3. must know its parent
 *    can have 0 or 1 parent
 * 4. must know its attributes
 *    can have 0 - n attributes
 * @author Adam Dornak original
 * @author Petr Plsek refactored
 */
trait THtmlElement
{
    use TAttributes;
    use TParent;

    /** @var string  */
    protected $template = '';
    /** @var IHtmlElement[] */
    protected $children = [];
    /** @var string */
    protected $childDelimiter = PHP_EOL;
    /** @var string|null */
    protected $alias = null;

    /**
     * Returns object alias
     * @return string|null
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * Render element
     * @return string
     */
    public function render(): string
    {
        return sprintf($this->template, $this->renderAttributes(), $this->renderChildren());
    }

    /**
     * render children into serialized strings
     * @return string
     */
    public function renderChildren(): string
    {
        return implode($this->childDelimiter, array_map([$this, 'renderChild'], $this->children));
    }

    protected function renderChild(IHtmlElement $child): string
    {
        return $child->render();
    }

    /**
     * Add child on stack end or replace the current one (if they have same alias)
     * @param IHtmlElement|string $child
     * @param string|null $alias - key for lookup; beware of empty strings
     * @param bool $merge merge with original element if already exists
     * @param bool $inherit inherit properties from current element
     */
    public final function addChild($child, $alias = null, bool $merge = false, bool $inherit = false): void
    {
        if ($child instanceof IHtmlElement) {
            if (!$this->checkAlias($alias)) {
                $alias = $child->getAlias();
            }
        } else {
            $alias = $this->checkAlias($alias) ? strval($alias) : null ;
            $child = new Text(strval($child), $alias);
        }
        $child->setParent($this);

        if ($this->checkAlias($alias)) {
            $child = $merge && $this->__isset($alias) ? $this->children[$alias]->merge($child) : $child ;
            $this->children[$alias] = $inherit ? $this->inherit($child) : $child ;
        } else {
            $this->children[] = $child;
        }

    }

    protected final function checkAlias($alias): bool
    {
        return !(is_null($alias) || ('' === $alias) || (is_object($alias)) || is_resource($alias) );
    }

    /**
     * Merge this element with child and its attributes
     * @param IHtmlElement $child
     */
    public function merge(IHtmlElement $child): void
    {
        $this->setChildren($child->getChildren());
        $this->setAttributes($child->getAttributes());
    }

    /**
     * Inheritance - set properties of this object into the child
     * @param IHtmlElement $child
     * @return IHtmlElement
     */
    public function inherit(IHtmlElement $child): IHtmlElement
    {
        $element = clone $child;
        $element->addAttributes($this->getAttributes());
        $element->setChildren($this->getChildren());
        return $element;
    }

    /**
     * Remove child by key
     * @param string|int $childAlias
     */
    public final function removeChild($childAlias): void
    {
        if (isset($this->children[$childAlias])) {
            unset($this->children[$childAlias]);
        }
    }

    /**
     * Return last child
     * @return IHtmlElement|null
     */
    public final function lastChild(): ?IHtmlElement
    {
        $last = end($this->children);
        return $last === false ? null : $last ;
    }

    /**
     * Set children of element
     * @param iterable|string[]|IHtmlElement[] $children
     */
    public final function setChildren(iterable $children = []): void
    {
        foreach ($children as $alias => $child) {
            $this->addChild(
                $child,
                is_numeric($alias) && $this->checkAlias($child->getAlias()) ? $child->getAlias() : $alias
            );
        }
    }

    /**
     * Return all children as iterator
     * @return Traversable IHtmlElement
     */
    public final function getChildren(): Traversable
    {
        yield from $this->children;
    }

    /**
     * Automatic access to child via Element->childAlias()
     * @param string|int $alias
     * @return IHtmlElement|null
     */
    public final function __get($alias)
    {
        return $this->__isset($alias) ? $this->children[$alias] : null ;
    }

    /**
     * Set child directly by setting a property of this class
     * @param string|int $alias
     * @param mixed $value
     */
    public final function __set($alias, $value)
    {
        $this->addChild($value, $alias);
    }

    /**
     * Call isset() for protected or private variables
     * @param string|int $alias
     * @return bool
     */
    public final function __isset($alias)
    {
        return isset($this->children[$alias]);
    }

    /**
     * Call empty() for protected or private variables
     * @param string|int $alias
     * @return bool
     */
    public final function __empty($alias)
    {
        return empty($this->children[$alias]);
    }

    public function __call($method, $args)
    {
        if (count($args) == 1) {
            $this->setAttribute($method, $args[0]);
        } elseif (count($args) == 0) {
            return $this->getAttribute($method);
        }
        return $this;
    }
}