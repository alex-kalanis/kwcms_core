<?php

namespace kalanis\kw_files\Interfaces;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Node;
use kalanis\kw_paths\PathsException;


/**
 * Interface IProcessDirs
 * @package kalanis\kw_files\Interfaces
 * Process dirs in basic ways
 */
interface IProcessDirs
{
    /**
     * @param string[] $entry
     * @param bool $deep
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function createDir(array $entry, bool $deep = false): bool;

    /**
     * @param string[] $entry
     * @param bool $loadRecursive
     * @param bool $wantSize
     * @throws FilesException
     * @throws PathsException
     * @return array<Node>
     */
    public function readDir(array $entry, bool $loadRecursive = false, bool $wantSize = false): array;

    /**
     * @param string[] $source
     * @param string[] $dest
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function copyDir(array $source, array $dest): bool;

    /**
     * @param string[] $source
     * @param string[] $dest
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function moveDir(array $source, array $dest): bool;

    /**
     * @param string[] $entry
     * @param bool $deep
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function deleteDir(array $entry, bool $deep = false): bool;
}
