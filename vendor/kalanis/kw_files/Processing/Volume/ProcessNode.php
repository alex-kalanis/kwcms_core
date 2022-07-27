<?php

namespace kalanis\kw_files\Processing\Volume;


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

    protected function fullPath(array $path): string
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . $this->compactName($path);
    }
}
