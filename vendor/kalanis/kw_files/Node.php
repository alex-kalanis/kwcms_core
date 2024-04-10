<?php

namespace kalanis\kw_files;


use kalanis\kw_files\Interfaces\ITypes;


/**
 * Class Node
 * @package kalanis\kw_files
 * File/dir object - similar to the SplFileInfo, but for passing as class over storages implemented externally
 * Passed paths are in following style:
 * - empty for current root node from lookup
 * - array containing path to nodes "somewhere" in tree
 * - then it's possible to reconstruct the path just by using array_merge()
 */
class Node
{
    /** @var array<string> */
    protected array $path = [];
    protected int $size = 0;
    protected string $type = ITypes::TYPE_UNKNOWN;

    /**
     * @param string[] $path only from wanted dir, not full path
     * @param int $size
     * @param string $type
     * @return $this
     */
    public function setData(array $path = [], int $size = 0, string $type = ITypes::TYPE_UNKNOWN): self
    {
        $this->path = $path;
        $this->size = $size;
        $this->type = $type;
        return $this;
    }

    /**
     * Gets the full path
     * @return array<string> the path to the file.
     */
    public function getPath(): array
    {
        return $this->path;
    }

    /**
     * Gets file size
     * @return int The filesize in bytes for files, the number of items for dirs
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Gets file type
     * @return string A string representing the type of the entry.
     * May be one of file, link or dir
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Tells if the object references a regular file
     * @return bool true if the file exists and is a regular file (not a link), false otherwise.
     */
    public function isFile(): bool
    {
        return ITypes::TYPE_FILE === $this->getType();
    }

    /**
     * Tells if the file is a directory
     * @return bool true if a directory, false otherwise.
     */
    public function isDir(): bool
    {
        return ITypes::TYPE_DIR === $this->getType();
    }
}
