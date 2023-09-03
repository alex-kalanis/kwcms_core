<?php

namespace kalanis\kw_modules\Parser;


/**
 * Class Record
 * @package kalanis\kw_modules\Parser
 * Single record about module from page
 */
class Record
{
    /** @var string */
    protected $moduleName = '';
    /** @var array<string|int, string|int|float|bool|array<string|int>> */
    protected $params = [];
    /** @var string[] */
    protected $path = [];
    /** @var string */
    protected $toChange = '';
    /** @var string */
    protected $whatReplace = '';

    public function setModuleName(string $moduleName): void
    {
        $this->moduleName = $moduleName;
    }

    /**
     * @param array<string|int, string|int|float|bool|array<string|int>> $params
     */
    public function setParams(array $params = []): void
    {
        $this->params = $params;
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

    /**
     * @return array<string|int, string|int|float|bool|array<string|int>>
     */
    public function getParams(): array
    {
        return $this->params;
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
