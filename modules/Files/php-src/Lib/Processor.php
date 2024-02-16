<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Extended\FindFreeName;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_input\Interfaces\IFileEntry;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;


/**
 * Class DirProcessor
 * @package KWCMS\modules\Files\Lib
 * Process dirs by file processing engine
 */
class Processor
{
    use TToString;

    /** @var string[] */
    protected $userPath = [];
    /** @var string[] */
    protected $workPath = [];
    /** @var CompositeAdapter */
    protected $files = null;

    public function __construct(CompositeAdapter $files)
    {
        $this->files = $files;
    }

    /**
     * @param string[] $userPath where is user set with his account
     * @return Processor
     */
    public function setUserPath(array $userPath): self
    {
        $this->userPath = $userPath;
        return $this;
    }

    /**
     * @param string[] $workPath where is user walking now
     * @return Processor
     */
    public function setWorkPath(array $workPath): self
    {
        $this->workPath = $workPath;
        return $this;
    }

    /**
     * @param string $entry
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function createDir(string $entry): bool
    {
        return $this->files->createDir(array_merge($this->userPath, $this->workPath, [Stuff::filename($entry)]));
    }

    /**
     * @param string $entry
     * @param string $to
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function copyDir(string $entry, string $to): bool
    {
        return $this->files->copyDir(array_merge(
            $this->userPath,
            $this->workPath,
            Stuff::pathToArray($entry)
        ), array_merge(
            $this->userPath,
            Stuff::pathToArray($to),
            [Stuff::filename($entry)]
        ));
    }

    /**
     * @param string $entry
     * @param string $to
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function moveDir(string $entry, string $to): bool
    {
        return $this->files->moveDir(array_merge(
            $this->userPath,
            $this->workPath,
            Stuff::pathToArray($entry)
        ), array_merge(
            $this->userPath,
            Stuff::pathToArray($to),
            [Stuff::filename($entry)]
        ));
    }

    /**
     * @param string $entry
     * @param string $to
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function renameDir(string $entry, string $to): bool
    {
        return $this->files->moveDir(array_merge(
            $this->userPath,
            $this->workPath,
            [Stuff::filename($entry)]
        ), array_merge(
            $this->userPath,
            $this->workPath,
            [Stuff::filename($to)]
        ));
    }

    /**
     * @param string $entry
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function deleteDir(string $entry): bool
    {
        return $this->files->deleteDir(array_merge(
            $this->userPath,
            $this->workPath,
            [Stuff::filename($entry)]
        ));
    }

    /**
     * @param string $name
     * @throws FilesException
     * @throws PathsException
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
        $lib = new FindFreeName($this->files->getNode());
        return $lib->findFreeName(array_merge($this->userPath, $this->workPath), $fileName, $ext);
    }

    /**
     * @param IFileEntry $file
     * @param string $targetName
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function uploadFile(IFileEntry $file, string $targetName): bool
    {
        $stream = fopen($file->getTempName(), 'rb+');
        return $this->files->saveFile(array_merge(
            $this->userPath,
            $this->workPath,
            [Stuff::filename($targetName)]
        ), $stream);
    }

    /**
     * @param string $entry
     * @param int|null $offset
     * @param int|null $length
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    public function readFile(string $entry, ?int $offset = null, ?int $length = null): string
    {
        return $this->toString($entry, $this->files->readFile(array_merge(
            $this->userPath,
            $this->workPath,
            [Stuff::filename($entry)]
        ), $offset, $length));
    }

    /**
     * @param string $entry
     * @param string $to
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function copyFile(string $entry, string $to): bool
    {
        return $this->files->copyFile(array_merge(
            $this->userPath,
            $this->workPath,
            Stuff::pathToArray($entry)
        ), array_merge(
            $this->userPath,
            Stuff::pathToArray($to),
            [Stuff::filename($entry)]
        ));
    }

    /**
     * @param string $entry
     * @param string $to
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function moveFile(string $entry, string $to): bool
    {
        return $this->files->moveFile(array_merge(
            $this->userPath,
            $this->workPath,
            Stuff::pathToArray($entry)
        ), array_merge(
            $this->userPath,
            Stuff::pathToArray($to),
            [Stuff::filename($entry)]
        ));
    }

    /**
     * @param string $entry
     * @param string $to
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function renameFile(string $entry, string $to): bool
    {
        return $this->files->moveFile(array_merge(
            $this->userPath,
            $this->workPath,
            [Stuff::filename($entry)]
        ), array_merge(
            $this->userPath,
            $this->workPath,
            [Stuff::filename($to)]
        ));
    }

    /**
     * @param string $entry
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function deleteFile(string $entry): bool
    {
        return $this->files->deleteFile(array_merge(
            $this->userPath,
            $this->workPath,
            [Stuff::filename($entry)]
        ));
    }
}
