<?php

namespace kalanis\kw_table\core\Table\Columns;


use kalanis\kw_connect\core\Interfaces\IRow;
use kalanis\kw_table\core\Interfaces\Form\IField;
use kalanis\kw_table\core\Interfaces\Table\IColumn;
use kalanis\kw_table\core\Table\AStyle;


/**
 * Class AColumn
 * @package kalanis\kw_table\Table\Columns
 */
abstract class AColumn extends AStyle implements IColumn
{
    protected $source;

    /** @var string */
    protected $sourceName = '';
    /** @var string */
    protected $filterName = '';
    /** @var bool */
    protected $sortable = false;
    /** @var IField|null */
    protected $headerFilterField = null;
    /** @var IField|null */
    protected $footerFilterField = null;
    /** @var string|null */
    protected $headerText = '';

    /**
     * @param string $text
     * @return $this
     */
    public function setHeaderText(string $text)
    {
        $this->headerText = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeaderText(): string
    {
        return is_null($this->headerText) ? $this->getFilterName() : $this->headerText ;
    }

    public function translate(IRow $source): string
    {
        return $this->formatData($this->getValue($source));
    }

    public function getSourceName(): string
    {
        return $this->sourceName;
    }

    public function getFilterName(): string
    {
        return empty($this->filterName) ? $this->sourceName : $this->filterName ;
    }

    /**
     * Returns value from row
     * @param IRow $source
     * @return mixed|null
     */
    public function getValue(IRow $source)
    {
        return $this->value($source, $this->sourceName);
    }

    /**
     * @param IRow $source
     * @param      $override
     * @return mixed
     */
    protected function getOverrideValue(IRow $source, $override)
    {
        return $this->value($source, $override);
    }

    /**
     * @param IRow $source
     * @param      $property
     * @return mixed
     */
    protected function value(IRow $source, $property)
    {
        return $source->getValue($property);
    }

    /**
     * Prida wrap tag
     * @param        $htmlTag
     * @param string $attributes
     * @return $this
     */
    public function addWrapper($htmlTag, $attributes = '')
    {
        $this->wrappers[$htmlTag] = $attributes;
        return $this;
    }

    /**
     * Naformatuje data
     * @param string $data
     * @return string
     */
    protected function formatData($data)
    {
        foreach ($this->wrappers as $tag => $attribute) {
            $data = sprintf('<%s %s>%s</%s>', $tag, $attribute, $data, $tag);
        }
        return $data;
    }

    public function isSortable(): bool
    {
        return $this instanceof IColumn;
    }

    public function hasHeaderFilterField(): bool
    {
        return $this->headerFilterField instanceof IField;
    }

    public function hasFooterFilterField(): bool
    {
        return $this->footerFilterField instanceof IField;
    }

    public function setHeaderFiltering(?IField $field): self
    {
        $this->headerFilterField = $field;
        return $this;
    }

    public function setFooterFiltering(IField $field)
    {
        $this->footerFilterField = $field;
        return $this;
    }

    public function getHeaderFilterField(): ?IField
    {
        return $this->headerFilterField;
    }

    public function getFooterFilterField(): ?IField
    {
        return $this->footerFilterField;
    }
}
