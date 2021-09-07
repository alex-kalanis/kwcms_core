<?php

namespace kalanis\kw_menu\Interfaces;


use kalanis\kw_menu\MenuException;
use Traversable;


/**
 * Interface IDataSource
 * @package kalanis\kw_menu\Interfaces
 * Which actions are supported by data sources
 * Work with directories or other storage devices
 */
interface IDataSource
{
    /**
     * @param string $metaFile
     * @return bool
     * @throws MenuException
     */
    public function exists(string $metaFile): bool;

    /**
     * @param string $metaFile
     * @return string
     * @throws MenuException
     */
    public function load(string $metaFile): string;

    /**
     * @param string $metaFile
     * @param string $content
     * @return bool
     * @throws MenuException
     */
    public function save(string $metaFile, string $content): bool;

    /**
     * @param string $dir
     * @return Traversable
     * @throws MenuException
     */
    public function getFiles(string $dir): Traversable;
}
