<?php

namespace kalanis\kw_modules\ModulesLists;


/**
 * Class Record
 * @package kalanis\kw_modules\Lists
 * Single record about module from configuration
 */
class Record
{
    protected string $moduleName = '';
    /** @var array<string|int, string|int|float|bool|array<string|int>> */
    protected array $params = [];
    protected bool $enabled = false;

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

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
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

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
