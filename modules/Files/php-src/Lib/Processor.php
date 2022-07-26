<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessDirs;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_files\Node;
use kalanis\kw_input\Interfaces\IFileEntry;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Stuff;


/**
 * Class DirProcessor
 * @package KWCMS\modules\Files\Lib
 * Process dirs by file processing engine
 */
class Processor
{
    /** @var Node */
    protected $currentNode = null;
    /** @var IProcessDirs */
    protected $dirProcessor = null;
    /** @var IProcessFiles */
    protected $fileProcessor = null;

    public function __construct(IProcessFiles $fileProcessor, IProcessDirs $dirProcessor, Node $currentNode)
    {
        $this->currentNode = $currentNode;
        $this->dirProcessor = $dirProcessor;
        $this->fileProcessor = $fileProcessor;
    }

    /**
     * @param string $entry
     * @throws FilesException
     * @return bool
     */
    public function createDir(string $entry): bool
    {
        $path = $this->currentNode->getPath() + [Stuff::filename($entry)];
        return $this->dirProcessor->createDir($path);
    }

    /**
     * @param string $entry
     * @param string $to
     * @throws FilesException
     * @return bool
     */
    public function copyDir(string $entry, string $to): bool
    {
        $source = $this->currentNode->getPath() + Stuff::pathToArray($entry);
        $target = Stuff::pathToArray($to) + [Stuff::filename($entry)];
        return $this->dirProcessor->copyDir($source, $target);
    }

    /**
     * @param string $entry
     * @param string $to
     * @throws FilesException
     * @return bool
     */
    public function moveDir(string $entry, string $to): bool
    {
        $source = $this->currentNode->getPath() + Stuff::pathToArray($entry);
        $target = Stuff::pathToArray($to) + [Stuff::filename($entry)];
        return $this->dirProcessor->moveDir($source, $target);
    }

    /**
     * @param string $entry
     * @param string $to
     * @throws FilesException
     * @return bool
     */
    public function renameDir(string $entry, string $to): bool
    {
        $source = $this->currentNode->getPath() + Stuff::pathToArray($entry);
        $target = $this->currentNode->getPath() + Stuff::pathToArray($to);
        return $this->dirProcessor->moveDir($source, $target);
    }

    /**
     * @param string $entry
     * @throws FilesException
     * @return bool
     */
    public function deleteDir(string $entry): bool
    {
        $path = $this->currentNode->getPath() + [Stuff::filename($entry)];
        return $this->dirProcessor->deleteDir($path);
    }

    /**
     * @param string $name
     * @throws FilesException
     * @return string
     */
    public function findFreeName(string $name): string
    {
        $name = Stuff::canonize($name);
        $ext = Stuff::fileExt($name);
        if (0 < mb_strlen($ext)) {
            $ext = IPaths::SPLITTER_DOT . $ext;
        }
        $fileName = Stuff::fileBase($name);
        return $this->fileProcessor->findFreeName([$fileName], $ext);
    }

    /**
     * @param IFileEntry $file
     * @param string $targetName
     * @throws FilesException
     * @return bool
     */
    public function uploadFile(IFileEntry $file, string $targetName): bool
    {
        $stream = fopen($file->getTempName(), 'rb+');
        $path = $this->currentNode->getPath() + [Stuff::filename($targetName)];
        return $this->fileProcessor->saveFile($path, $stream);
    }

    /**
     * @param string $entry
     * @param int|null $offset
     * @param int|null $length
     * @throws FilesException
     * @return string
     */
    public function readFile(string $entry, ?int $offset = null, ?int $length = null): string
    {
        return $this->fileProcessor->readFile(Stuff::pathToArray($entry), $offset, $length);
    }

    /**
     * @param string $entry
     * @param string $to
     * @throws FilesException
     * @return bool
     */
    public function copyFile(string $entry, string $to): bool
    {
        $source = $this->currentNode->getPath() + Stuff::pathToArray($entry);
        $target = Stuff::pathToArray($to) + [Stuff::filename($entry)];
        return $this->fileProcessor->copyFile($source, $target);
    }

    /**
     * @param string $entry
     * @param string $to
     * @throws FilesException
     * @return bool
     */
    public function moveFile(string $entry, string $to): bool
    {
        $source = $this->currentNode->getPath() + Stuff::pathToArray($entry);
        $target = Stuff::pathToArray($to) + [Stuff::filename($entry)];
        return $this->fileProcessor->moveFile($source, $target);
    }

    /**
     * @param string $entry
     * @param string $to
     * @throws FilesException
     * @return bool
     */
    public function renameFile(string $entry, string $to): bool
    {
        $source = $this->currentNode->getPath() + Stuff::pathToArray($entry);
        $target = $this->currentNode->getPath() + Stuff::pathToArray($to);
        return $this->fileProcessor->moveFile($source, $target);
    }

    /**
     * @param string $entry
     * @throws FilesException
     * @return bool
     */
    public function deleteFile(string $entry): bool
    {
        $path = $this->currentNode->getPath() + [Stuff::filename($entry)];
        return $this->fileProcessor->deleteFile($path);
    }
}
