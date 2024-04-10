<?php

namespace kalanis\kw_files\Interfaces;


use kalanis\kw_files\FilesException;
use kalanis\kw_paths\PathsException;


/**
 * Interface IProcessFileStreams
 * @package kalanis\kw_files\Interfaces
 * Process files as streams ways
 */
interface IProcessFileStreams
{
    /**
     * @param string[] $entry
     * @param resource $content
     * @param int<0, max> $mode
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function saveFileStream(array $entry, $content, int $mode = 0): bool;

    /**
     * @param string[] $entry
     * @throws FilesException
     * @throws PathsException
     * @return resource
     */
    public function readFileStream(array $entry);

    /**
     * @param string[] $source
     * @param string[] $dest
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function copyFileStream(array $source, array $dest): bool;

    /**
     * @param string[] $source
     * @param string[] $dest
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function moveFileStream(array $source, array $dest): bool;
}
