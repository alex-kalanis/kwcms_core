<?php

namespace kalanis\kw_files;


/**
 * Class Node
 * @package kalanis\kw_files
 * File/dir object - similar to the SplFileInfo, but for passing as class over storages implemented externally
 */
class Node
{
    /** @var array */
    protected $path = [];
    /** @var int */
    protected $size = 0;
    /** @var string */
    protected $type = 'none';

    public function setData(array $path = [], int $size = 0, string $type = 'none'): self
    {
        $this->path = $path;
        $this->size = $size;
        $this->type = $type;
        return $this;
    }

    /**
     * Gets the path without filename
     * @return array the path to the file.
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
        return 'file' === $this->getType();
    }

    /**
     * Tells if the file is a directory
     * @return bool true if a directory, false otherwise.
     */
    public function isDir(): bool
    {
        return 'dir' === $this->getType();
    }
}
