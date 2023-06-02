<?php

namespace kalanis\kw_clipr\Interfaces;


use kalanis\kw_clipr\CliprException;


/**
 * Interface ISubLoaders
 * @package kalanis\kw_clipr\Interfaces
 * From where you can get your tasks - listing paths
 */
interface ISubLoaders
{
    /**
     * @throws CliprException
     * @return ILoader[] Array of loaders available in class
     */
    public function getLoaders(): array;
}
