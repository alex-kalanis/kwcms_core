<?php

namespace kalanis\kw_files\Access;


use kalanis\kw_files\Interfaces;


/**
 * Class CompositeAdapter
 * @package kalanis\kw_files\Access
 * Pass work with files in storage in one class
 */
class CompositeAdapter implements Interfaces\IProcessNodes, Interfaces\IProcessDirs, Interfaces\IProcessFiles
{
    /** @var Interfaces\IProcessNodes */
    protected $libNode = null;
    /** @var Interfaces\IProcessDirs */
    protected $libDir = null;
    /** @var Interfaces\IProcessFiles */
    protected $libFile = null;

    public function __construct(Interfaces\IProcessNodes $libNode, Interfaces\IProcessDirs $libDir, Interfaces\IProcessFiles $libFile)
    {
        $this->libNode = $libNode;
        $this->libDir = $libDir;
        $this->libFile = $libFile;
    }

    public function createDir(array $entry, bool $deep = false): bool
    {
        return $this->libDir->createDir($entry, $deep);
    }

    public function readDir(array $entry, bool $loadRecursive = false, bool $wantSize = false): array
    {
        return $this->libDir->readDir($entry, $loadRecursive, $wantSize);
    }

    public function copyDir(array $source, array $dest): bool
    {
        return $this->libDir->copyDir($source, $dest);
    }

    public function moveDir(array $source, array $dest): bool
    {
        return $this->libDir->moveDir($source, $dest);
    }

    public function deleteDir(array $entry, bool $deep = false): bool
    {
        return $this->libDir->deleteDir($entry, $deep);
    }

    public function saveFile(array $entry, $content, ?int $offset = null): bool
    {
        return $this->libFile->saveFile($entry, $content, $offset);
    }

    public function readFile(array $entry, ?int $offset = null, ?int $length = null)
    {
        return $this->libFile->readFile($entry, $offset, $length);
    }

    public function copyFile(array $source, array $dest): bool
    {
        return $this->libFile->copyFile($source, $dest);
    }

    public function moveFile(array $source, array $dest): bool
    {
        return $this->libFile->moveFile($source, $dest);
    }

    public function deleteFile(array $entry): bool
    {
        return $this->libFile->deleteFile($entry);
    }

    public function exists(array $entry): bool
    {
        return $this->libNode->exists($entry);
    }

    public function isReadable(array $entry): bool
    {
        return $this->libNode->isReadable($entry);
    }

    public function isWritable(array $entry): bool
    {
        return $this->libNode->isWritable($entry);
    }

    public function isDir(array $entry): bool
    {
        return $this->libNode->isDir($entry);
    }

    public function isFile(array $entry): bool
    {
        return $this->libNode->isFile($entry);
    }

    public function size(array $entry): ?int
    {
        return $this->libNode->size($entry);
    }

    public function created(array $entry): ?int
    {
        return $this->libNode->created($entry);
    }

    public function getNode(): Interfaces\IProcessNodes
    {
        return $this->libNode;
    }

    public function getDir(): Interfaces\IProcessDirs
    {
        return $this->libDir;
    }

    public function getFile(): Interfaces\IProcessFiles
    {
        return $this->libFile;
    }
}
