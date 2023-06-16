<?php

namespace kalanis\kw_files\Interfaces;


use kalanis\kw_files\FilesException;
use kalanis\kw_paths\PathsException;


/**
 * Interface IProcessNodes
 * @package kalanis\kw_files\Interfaces
 * Process entries in basic ways
 */
interface IProcessNodes
{
    const STORAGE_NODE_KEY = "\eNODE\e";

    /**
     * @param string[] $entry
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function exists(array $entry): bool;

    /**
     * @param string[] $entry
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function isReadable(array $entry): bool;

    /**
     * @param string[] $entry
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function isWritable(array $entry): bool;

    /**
     * @param string[] $entry
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function isDir(array $entry): bool;

    /**
     * @param string[] $entry
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function isFile(array $entry): bool;

    /**
     * @param string[] $entry
     * @throws FilesException
     * @throws PathsException
     * @return int<0, max>|null
     */
    public function size(array $entry): ?int;

    /**
     * @param string[] $entry
     * @throws FilesException
     * @throws PathsException
     * @return int|null
     */
    public function created(array $entry): ?int;
}
