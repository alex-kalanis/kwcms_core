<?php

namespace kalanis\kw_forms\Controls;


use kalanis\kw_rules\Exceptions\RuleException;


/**
 * Trait TSubErrors
 * @package kalanis\kw_forms\Controls
 * Trait for processing errors
 */
trait TSubErrors
{
    /** @var RuleException[][] */
    protected $errors = [];

    /**
     * @return RuleException[][]
     */
    public function getValidatedErrors(): array
    {
        return $this->errors;
    }
}
