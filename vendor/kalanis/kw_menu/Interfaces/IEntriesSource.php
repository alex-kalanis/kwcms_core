<?php

namespace kalanis\kw_menu\Interfaces;


use kalanis\kw_menu\MenuException;
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
     * @param string $dir
     * @return Traversable
     * @throws MenuException
     */
    public function getFiles(string $dir): Traversable;
}
