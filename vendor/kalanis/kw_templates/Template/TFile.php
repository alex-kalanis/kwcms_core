<?php

namespace kalanis\kw_templates\Template;


use kalanis\kw_templates\TemplateException;


/**
 * Trait TFile
 * @package kalanis\kw_templates\Template
 * Trait for loading templates from files, not from code
 */
trait TFile
{

    /**
     * @return string
     * @throws TemplateException
     */
    protected function loadTemplate(): string
    {
        $path = $this->templatePath();
        $result = @file_get_contents($path);
        if (false === $result) {
            throw new TemplateException(sprintf('Template file %s not found', $path));
        }
        return $result;
    }

    abstract protected function templatePath(): string;
}
