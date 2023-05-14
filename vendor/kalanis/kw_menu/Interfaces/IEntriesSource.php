<?php

namespace kalanis\kw_menu\Interfaces;


use kalanis\kw_menu\MenuException;
use kalanis\kw_paths\PathsException;
use Traversable;


/**
 * Interface IEntriesSource
 * @package kalanis\kw_menu\Interfaces
 * Which actions are supported by data sources
 * Work with directories or other storage devices
 */
interface IEntriesSource
{
    /**
     * @param string[] $path
     * @throws MenuException
     * @throws PathsException
     * @return Traversable<string>
     */
    public function getFiles(array $path): Traversable;
}
