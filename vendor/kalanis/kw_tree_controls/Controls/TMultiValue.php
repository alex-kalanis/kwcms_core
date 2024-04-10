<?php

namespace kalanis\kw_tree_controls\Controls;


use kalanis\kw_forms\Controls;
use kalanis\kw_input\Interfaces\IFileEntry;


/**
 * Trait TMultiValue
 * @package kalanis\kw_tree_controls\Controls
 */
trait TMultiValue
{
    use Controls\TShorterKey;

    /** @var Controls\AControl[]|Controls\Checkbox[] */
    protected array $inputs = [];

    /**
     * @return array<string|int, string|int|float|bool|IFileEntry|null>
     */
    public function getValues(): array
    {
        $array = [];
        foreach ($this->inputs as $child) {
            if (empty($child->getValue())) {
                continue;
            }
            $array[] = $child->getValue();
        }
        return $array;
    }

    /**
     * Set values to all children
     * !! UNDEFINED values will be SET too !!
     * @param array<string|int, string|int|float|bool|IFileEntry|null> $array
     */
    public function setValues(array $array): void
    {
        foreach ($this->inputs as $child) {
            /** @var Controls\Checkbox $child */
            $shortKey = $this->shorterKey($child->getKey());
            $child->setValue(
                isset($array[$shortKey])
                && is_array($array[$shortKey])
                && in_array($child->getOriginalValue(), $array[$shortKey])
                    ? $child->getOriginalValue()
                    : (
                    isset($array[$child->getKey()])
                    && is_array($array[$child->getKey()])
                    && in_array($child->getOriginalValue(), $array[$child->getKey()])
                        // @codeCoverageIgnoreStart
                        // must got custom key - not happened in usual cases
                        ? $child->getOriginalValue()
                        // @codeCoverageIgnoreEnd
                        : ''
                )
            );
        }
    }
}
