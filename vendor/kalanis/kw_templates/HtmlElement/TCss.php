<?php

namespace kalanis\kw_templates\HtmlElement;


use kalanis\kw_templates\Interfaces\IAttributes;


/**
 * Trait TCss
 * @package kalanis\kw_templates\Template
 * Trait for work with cascade style sheets - via classes
 * Extend child of AHtmlElement
 * @author Adam Dornak original
 * @author Petr Plsek refactored
 */
trait TCss
{
    /**
     * Add class into attribute class
     * @param string $name
     * @return $this
     */
    public function addClass(string $name): self
    {
        $class = $this->getAttribute(IAttributes::ATTR_NAME_CLASS);
        if (!empty($class)) {
            $class = explode(IAttributes::ATTR_SEP_CLASS, $class);
            if (!in_array($name, $class)) {
                $class[] = $name;
                $this->setAttribute(IAttributes::ATTR_NAME_CLASS, implode(IAttributes::ATTR_SEP_CLASS, $class));
            }
        } else {
            $this->setAttribute(IAttributes::ATTR_NAME_CLASS, $name);
        }
        return $this;
    }

    /**
     * Remote class from attribute class
     * @param string $name
     * @return $this
     */
    public function removeClass(string $name): self
    {
        $class = $this->getAttribute(IAttributes::ATTR_NAME_CLASS);
        if (!empty ($class)) {
            $class = explode(IAttributes::ATTR_SEP_CLASS, $class);
            if (in_array($name, $class)) {
                $class = array_flip($class);
                unset ($class[$name]);
                $class = array_flip($class);
                $this->setAttribute(IAttributes::ATTR_NAME_CLASS, implode(IAttributes::ATTR_SEP_CLASS, $class));
            }
        }
        return $this;
    }

    abstract public function getAttribute(string $name);

    abstract public function setAttribute(string $name, string $value);
}
