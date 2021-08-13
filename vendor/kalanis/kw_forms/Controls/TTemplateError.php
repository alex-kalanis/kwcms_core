<?php

namespace kalanis\kw_forms\Controls;


trait TTemplateError
{
    // 1 text
    protected $templateError = '%s';

    /**
     * @return string
     */
    public function getTemplateError(): string
    {
        return $this->templateError;
    }

    /**
     * @param string $templateError
     */
    public function setTemplateError(string $templateError): void
    {
        $this->templateError = $templateError;
    }
}
