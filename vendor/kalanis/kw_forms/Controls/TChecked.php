<?php

namespace kalanis\kw_forms\Controls;


/**
 * Class TChecked
 * @package kalanis\kw_forms\Controls
 * Render input for selecting by radio checkbox
 */
trait TChecked
{
    public function setValue($value): void
    {
        $this->setChecked($value);
    }

    public function getValue()
    {
        return $this->getChecked() ? $this->originalValue : '' ;
    }

    /**
     * Set if radio is checked
     * @param string $value
     * @return $this
     */
    protected function setChecked($value): self
    {
        if (!empty($value) && (strval($value) !== 'none')) {
            $this->setAttribute('checked', 'checked');
        } else {
            $this->removeAttribute('checked');
        }
        return $this;
    }

    /**
     * Get if radio is checked
     * @return bool
     */
    protected function getChecked(): bool
    {
        return ('checked' == $this->getAttribute('checked'));
    }

    abstract public function setAttribute(string $name, string $value): void;

    abstract public function removeAttribute(string $name): void;

    abstract public function getAttribute(string $name): ?string;
}
