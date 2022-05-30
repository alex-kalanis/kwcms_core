<?php

namespace kalanis\kw_forms\Controls;


use kalanis\kw_forms\Interfaces\IContainsControls;
use kalanis\kw_rules\Validate;


/**
 * Class AnyControl
 * @package kalanis\kw_forms\Controls
 * Form element for rendering other controls
 */
class AnyControl extends AControl implements IContainsControls
{
    use TSubControls;
    use TSubErrors;

    protected $needAll = false;

    protected $templateLabel = '';

    public function removeControl(string $key): void
    {
        if (isset($this->controls[$key])) {
            unset($this->controls[$key]);
        }
    }

    public function hasControl(string $key): bool
    {
        return !empty($this->getControl($key));
    }

    public function needAll(bool $yes = false): void
    {
        $this->needAll = $yes;
    }

    public function validateControls(Validate $validate): bool
    {
        $this->errors = [];
        $validation = true;
        foreach ($this->controls as &$child) {
            if ($child instanceof IContainsControls) {
                $result = $child->validateControls($validate);
                if ($result && !$this->needAll) {
                    $this->errors = [];
                    return true;
                }
                $validation &= $result;
                $this->errors += $child->getValidatedErrors();
            } elseif ($child instanceof AControl) {
                $result = $validate->validate($child);
                if ($result && !$this->needAll) {
                    $this->errors = [];
                    return true;
                }
                $validation &= $result;
                $this->errors += $validate->getErrors();
            }
        }
        return $validation;
    }

    public function render(): string
    {
        return '';
    }

    public function renderLabel($attributes = []): string
    {
        return '';
    }

    public function renderInput($attributes = null): string
    {
        return '';
    }

    public function renderErrors($errors): string
    {
        return '';
    }

    public function setLabel(?string $value): void
    {
    }

    public function getLabel(): ?string
    {
        return null;
    }

    public function setValue($value): void
    {
    }

    public function getValue()
    {
        return null;
    }
}
