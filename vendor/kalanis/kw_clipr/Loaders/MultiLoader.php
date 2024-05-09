<?php

namespace kalanis\kw_clipr\Loaders;


use kalanis\kw_clipr\Interfaces\ILoader;
use kalanis\kw_clipr\Interfaces\ISubLoaders;
use kalanis\kw_clipr\Tasks\ATask;


/**
 * Class MultiLoader
 * @package kalanis\kw_clipr\Tasks
 * Load from multiple sources
 */
class MultiLoader implements ILoader, ISubLoaders
{
    /** @var ILoader[] */
    protected array $subLoaders = [];
    /** @var ATask[] */
    protected array $loadedClasses = [];

    public static function init(): self
    {
        return new static();
    }

    final public function __construct()
    {
    }

    public function addLoader(ILoader $loader): self
    {
        $name = get_class($loader);
        $this->subLoaders[$name] = $loader;
        return $this;
    }

    public function getTask(string $classFromParam): ?ATask
    {
        foreach ($this->subLoaders as $loader) {
            if ($module = $loader->getTask($classFromParam)) {
                return $module;
            }
        }
        return null;
    }

    public function getLoaders(): array
    {
        return $this->subLoaders;
    }
}
