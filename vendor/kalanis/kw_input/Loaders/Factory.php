<?php

namespace kalanis\kw_input\Loaders;


use kalanis\kw_input\Interfaces\IEntry;


/**
 * Class Factory
 * @package kalanis\kw_input\Loaders
 * Loading factory
 */
class Factory
{
    /** @var ALoader[] */
    protected array $loaders;

    public function getLoader(string $source): ALoader
    {
        if (isset($this->loaders[$source])) {
            return $this->loaders[$source];
        }
        $loader = $this->select($source);
        $this->loaders[$source] = $loader;
        return $loader;
    }

    protected function select(string $source): ALoader
    {
        switch ($source) {
            case IEntry::SOURCE_FILES:
                return new File();
            case IEntry::SOURCE_JSON:
                return new Json();
            case IEntry::SOURCE_CLI:
                return new CliEntry();
            default:
                return new Entry();
        }
    }
}
