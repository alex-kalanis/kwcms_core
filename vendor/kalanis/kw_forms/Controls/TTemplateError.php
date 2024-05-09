<?php

namespace kalanis\kw_forms\Controls;


/**
 * Trait TTemplateError
 * @package kalanis\kw_forms\Controls
 */
trait TTemplateError
{
    // 1 text
    protected string $templateError = '%s';

    public function getTemplateError(): string
    {
        return $this->templateError;
    }

    public function setTemplateError(string $templateError): void
    {
        $this->templateError = $templateError;
    }
}
