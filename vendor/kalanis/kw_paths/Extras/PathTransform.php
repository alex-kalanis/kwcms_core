<?php

namespace kalanis\kw_paths\Extras;


use kalanis\kw_paths\Interfaces\IPATranslations;
use kalanis\kw_paths\Translations;


/**
 * Class PathTransform
 * @package kalanis\kw_paths\Extras
 * Just implementation of transformation of names
 */
class PathTransform
{
    use TPathTransform;

    /** @var IPATranslations */
    protected $lang = null;

    public static function get(IPATranslations $lang = null): self
    {
        return new self($lang);
    }

    public function __construct(IPATranslations $lang = null)
    {
        $this->lang = $lang ?: new Translations();
    }

    protected function noDirectoryDelimiterSet(): string
    {
        return $this->lang->paNoDirectoryDelimiterSet();
    }
}
