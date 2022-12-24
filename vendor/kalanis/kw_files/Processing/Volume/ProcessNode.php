<?php

namespace kalanis\kw_files\Processing\Volume;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessNodes;
use kalanis\kw_files\Processing\TPath;
use kalanis\kw_files\Processing\TPathTransform;


/**
 * Class ProcessNode
 * @package kalanis\kw_files\Processing\Volume
 * Process nodes in basic ways
 */
class ProcessNode implements IProcessNodes
{
    use TPath;
    use TPathTransform;

    public function __construct(string $path = '')
    {
        $this->setPath($path);
    }

    public function exists(array $entry): bool
    {
        return @file_exists($this->fullPath($entry));
    }

    public function isDir(array $entry): bool
    {
        return @is_dir($this->fullPath($entry));
    }

    public function isFile(array $entry): bool
    {
        return @is_file($this->fullPath($entry));
    }

    public function size(array $entry): ?int
    {
        $path = $this->fullPath($entry);
        $size = @filesize($path);
        return (false === $size) ? null : $size;
    }

    public function created(array $entry): ?int
    {
        $path = $this->fullPath($entry);
        $created = @filemtime($path);
        return (false === $created) ? null : $created;
    }

    /**
     * @param array<string> $path
     * @throws FilesException
     * @return string
     */
    protected function fullPath(array $path): string
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . $this->compactName($path);
    }
}
