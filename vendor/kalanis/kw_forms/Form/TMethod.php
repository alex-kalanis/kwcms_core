<?php

namespace kalanis\kw_forms\Form;


use kalanis\kw_input\Interfaces\IEntry;


/**
 * Trait TMethod
 * @package kalanis\kw_forms\Form
 * Trait to processing methods of form
 */
trait TMethod
{
    /**
     * Set transfer method of form
     * @param string $param
     * @return void
     */
    public function setMethod(string $param)
    {
        if (in_array($param, [IEntry::SOURCE_GET, IEntry::SOURCE_POST])) {
            $this->setAttribute('method', $param);
        }
    }

    /**
     * Get that method
     * @return string
     */
    public function getMethod()
    {
        return $this->getAttribute('method');
    }

    abstract public function setAttribute(string $name, string $value): void;

    abstract public function removeAttribute(string $name): void;

    abstract public function getAttribute(string $name): ?string;
}
