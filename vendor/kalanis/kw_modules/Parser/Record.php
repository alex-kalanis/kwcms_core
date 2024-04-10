<?php

namespace kalanis\kw_modules\Parser;


/**
 * Class Record
 * @package kalanis\kw_modules\Parser
 * Single record about module from page
 */
class Record
{
    protected string $moduleName = '';
    protected string $content = '';
    /** @var string[] */
    protected array $path = [];
    protected string $toChange = '';
    protected string $whatReplace = '';

    public function setModuleName(string $moduleName): void
    {
        $this->moduleName = $moduleName;
    }

    public function setContent(string $content = ''): void
    {
        $this->content = $content;
    }

    /**
     * @param string[] $path
     */
    public function setModulePath(array $path): void
    {
        $this->path = $path;
    }

    public function setContentToChange(string $content): void
    {
        $this->toChange = $content;
    }

    public function setWhatWillReplace(string $content): void
    {
        $this->whatReplace = $content;
    }

    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string[]
     */
    public function getModulePath(): array
    {
        return $this->path;
    }

    public function getToChange(): string
    {
        return $this->toChange;
    }

    public function getWillReplace(): string
    {
        return $this->whatReplace;
    }
}
