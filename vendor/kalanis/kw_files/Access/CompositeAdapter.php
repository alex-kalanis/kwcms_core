<?php

namespace kalanis\kw_files\Access;


use kalanis\kw_files\Interfaces;


/**
 * Class CompositeAdapter
 * @package kalanis\kw_files\Access
 * Pass work with files in storage in one class
 */
class CompositeAdapter implements Interfaces\IProcessNodes, Interfaces\IProcessDirs, Interfaces\IProcessFiles, Interfaces\IProcessFileStreams
{
    protected Interfaces\IProcessNodes $libNode;
    protected Interfaces\IProcessDirs $libDir;
    protected Interfaces\IProcessFiles $libFile;
    protected Interfaces\IProcessFileStreams $libStream;

    public function __construct(
        Interfaces\IProcessNodes $libNode,
        Interfaces\IProcessDirs $libDir,
        Interfaces\IProcessFiles $libFile,
        Interfaces\IProcessFileStreams $libStream
    )
    {
        $this->libNode = $libNode;
        $this->libDir = $libDir;
        $this->libFile = $libFile;
        $this->libStream = $libStream;
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

    public function saveFile(array $entry, string $content, ?int $offset = null, int $mode = 0): bool
    {
        return $this->libFile->saveFile($entry, $content, $offset, $mode);
    }

    public function readFile(array $entry, ?int $offset = null, ?int $length = null): string
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

    public function saveFileStream(array $entry, $content, int $mode = 0): bool
    {
        return $this->libStream->saveFileStream($entry, $content, $mode);
    }

    public function readFileStream(array $entry)
    {
        return $this->libStream->readFileStream($entry);
    }

    public function copyFileStream(array $source, array $dest): bool
    {
        return $this->libStream->copyFileStream($source, $dest);
    }

    public function moveFileStream(array $source, array $dest): bool
    {
        return $this->libStream->moveFileStream($source, $dest);
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

    public function getStream(): Interfaces\IProcessFileStreams
    {
        return $this->libStream;
    }
}
