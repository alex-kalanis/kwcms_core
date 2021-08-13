<?php

namespace kalanis\kw_templates;


use kalanis\kw_templates\HtmlElement\TCss;
use kalanis\kw_templates\HtmlElement\TStyles;


/**
 * Class HtmlElement
 * @package kalanis\kw_templates
 * Basic html element - for render simple nodes
 * @author Adam Dornak original
 * @author Petr Plsek refactored
 */
class HtmlElement extends AHtmlElement
{
    use TCss, TStyles;

    protected static $emptyElements = ['img','hr','br','input','meta','area','embed','keygen','link','param','frame'];

    /** @var string  element's name */
    private $name = '';

    public static function init(string $name, array $attributes = [])
    {
        return new static($name, $attributes);
    }

    public function __construct(string $name, array $attributes = [])
    {
        $name = str_ireplace(['<','>'],'', $name);
        $parts = explode(' ', $name, 2);
        $name = $parts[0];

        $this->name = $parts[0];

        if (in_array($this->name, static::$emptyElements)) {
            $this->template = "<{$name}%1\$s />";
        } else {
            $this->template = "<{$name}%1\$s>%2\$s</{$name}>";
        }

        $this->addAttributes($attributes);

        if (isset($parts[1])) {
            $this->addAttributes($parts[1]);
        }
    }
}