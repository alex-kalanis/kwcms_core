<?php

namespace kalanis\kw_files\Interfaces;


use kalanis\kw_files\FilesException;


/**
 * Interface IProcessNodes
 * @package kalanis\kw_files\Interfaces
 * Process entries in basic ways
 */
interface IProcessNodes
{
    /**
     * @param string[] $entry
     * @throws FilesException
     * @return bool
     */
    public function exists(array $entry): bool;

    /**
     * @param string[] $entry
     * @throws FilesException
     * @return bool
     */
    public function isDir(array $entry): bool;

    /**
     * @param string[] $entry
     * @throws FilesException
     * @return bool
     */
    public function isFile(array $entry): bool;

    /**
     * @param string[] $entry
     * @throws FilesException
     * @return int|null
     */
    public function size(array $entry): ?int;

    /**
     * @param string[] $entry
     * @throws FilesException
     * @return int|null
     */
    public function created(array $entry): ?int;
}
