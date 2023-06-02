<?php

namespace kalanis\kw_clipr\Interfaces;


use kalanis\kw_clipr\CliprException;


/**
 * Interface ITargetDirs
 * @package kalanis\kw_clipr\Interfaces
 * From where you can get your tasks - listing paths
 */
interface ITargetDirs extends ILoader
{
    /**
     * @throws CliprException
     * @return array<string, array<string>> Array of paths to get list of tasks from its directories
     */
    public function getPaths(): array;
}
