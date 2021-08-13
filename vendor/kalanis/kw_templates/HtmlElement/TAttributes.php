<?php

namespace kalanis\kw_templates\HtmlElement;


/**
 * Trait TAttributes
 * @package kalanis\kw_templates\Template
 * Trait for work with attributes
 * It's not necessary to have attributes directly in HtmlElement
 * @author Adam Dornak original
 * @author Petr Plsek refactored
 */
trait TAttributes
{
    /** @var string[] */
    protected $attributes = [];

    /**
     * Returns serialized attributes
     * Use $attributes param if is set
     * @param string|string[] $attributes
     * @return string
     */
    protected final function renderAttributes($attributes = null): string
    {
        $attributes = is_null($attributes) ? $this->attributes : $this->attributesParse($attributes);

        $return = '';
        foreach ($attributes as $name => $value) {
            $return .= ' ' . $name . '="' . $value . '"';
        }

        return $return;
    }

    /**
     * Add array of attributes into current object attributes
     * @param string|string[] $attributes
     */
    public final function addAttributes($attributes): void
    {
        $this->attributes = array_merge($this->attributes, $this->attributesParse($attributes));
    }

    /**
     * Change attributes in variable to 2-dimensional array
     * Expects array, discard rest
     * @param string|string[] $attributes
     * @return string[]
     */
    public final function attributesParse($attributes): array
    {
        $attributes = is_array($attributes) ? $attributes : $this->attributesParseString(strval($attributes));
        return $this->attributesParseArray($attributes);
    }

    /**
     * Change attributes in variable to 2-dimensional array
     * Expects array, discard rest
     * @param string[] $attributes
     * @return string[]
     */
    public final function attributesParseArray(array $attributes): array
    {
        $array = [];
        foreach ($attributes as $key => $val) {
            if (is_string($key)) {
                $key = strtolower($key);
                if (is_string($val) || is_numeric($val)) {
                    $val = strtolower($val);
                    $array[$key] = $val;
                } else if (is_array($val)) {
                    foreach ($val as &$_val) {
                        $_val = strtolower($_val);
                    }
                    $val = implode(';', $val);
                    $array[$key] = $val;
                }
            }
        }
        return $array;
    }

    /**
     * Change attributes to 2-dimensional array
     * Expects string like: width="100px" height='150px' style="color:red"
     * Discard rest
     * @param string $attributes
     * @return string[]
     */
    public final function attributesParseString(string $attributes): array
    {
        $array = [];
        $string = trim($attributes);
        if (preg_match_all('/([a-z]+)\=("|\')?(.+?)(?(2)\2)(\s|$)/', $string, $matches)) {
            for ($i = 0; $i < count($matches[1]); $i++) {
                $array[$matches[1][$i]] = trim($matches[3][$i]);
            }
        }
        return $array;
    }

    /**
     * Set attributes, leave nothing from previous ones
     * @param array $attributes
     */
    public final function setAttributes(array $attributes): void
    {
        $this->attributes = [];
        $this->addAttributes($attributes);
    }

    /**
     * Get all available attributes
     * @return string[]
     */
    public final function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Get attribute value
     * @param string $name
     * @return string|null
     */
    public final function getAttribute(string $name): ?string
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null ;
    }

    /**
     * Set attribute value
     * @param string $name
     * @param string $value
     */
    public final function setAttribute(string $name, string $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Remove attribute
     * @param string $name
     */
    public final function removeAttribute(string $name): void
    {
        if (isset($this->attributes[$name])) {
            unset($this->attributes[$name]);
        }
    }
}
