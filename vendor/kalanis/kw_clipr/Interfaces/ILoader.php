<?php

namespace kalanis\kw_clipr\Interfaces;


use kalanis\kw_clipr\CliprException;
use kalanis\kw_clipr\Tasks\ATask;


/**
 * Interface ILoader
 * @package kalanis\kw_clipr\Interfaces
 * From where you can get your tasks
 */
interface ILoader
{
    const DEFAULT_TASK = 'clipr\Info';

    /**
     * @param string $classFromParam
     * @return ATask|null
     * @throws CliprException
     */
    public function getTask(string $classFromParam): ?ATask;
}
