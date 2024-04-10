<?php

namespace kalanis\kw_files\Processing\Storage;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces;
use kalanis\kw_files\Traits;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Extras\TPathTransform;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class ProcessFileStream
 * @package kalanis\kw_files\Processing\Storage
 * Process files in many ways
 */
class ProcessFileStream implements Interfaces\IProcessFileStreams
{
    use Traits\TLang;
    use Traits\TToStream;
    use Traits\TToString;
    use TPathTransform;

    protected IStorage $storage;

    public function __construct(IStorage $storage, ?Interfaces\IFLTranslations $lang = null)
    {
        $this->setFlLang($lang);
        $this->storage = $storage;
    }

    public function saveFileStream(array $entry, $content, int $mode = 0, bool $throwErrorForParent = true): bool
    {
        $this->checkSupportedModes($mode);
        $path = $this->getStorageSeparator() . $this->filledName($this->compactName($entry, $this->getStorageSeparator()));
        try {
            $dstArr = new ArrayPath();
            $dstArr->setArray($entry);
            $tgt = $this->compactName($dstArr->getArrayDirectory(), $this->getStorageSeparator());
            if (!empty($tgt) && !$this->storage->exists($this->getStorageSeparator() . $tgt)) {
                // parent dir
                if ($throwErrorForParent) {
                    throw new FilesException($this->getFlLang()->flCannotSaveFile($path));
                } else {
                    return false;
                }
            }

            if (FILE_APPEND == $mode) {
                // put it somewhere, left the rest intact
                if ($this->storage->exists($path)) {
                    $target = $this->storage->read($path);
                } else {
                    $target = '';
                }
                return $this->storage->write($path, $target . $this->toString($path, $content));
            } else {
                return $this->storage->write($path, $this->toString($path, $content));
            }
        } catch (StorageException $ex) {
            throw new FilesException($this->getFlLang()->flCannotSaveFile($path), $ex->getCode(), $ex);
        }
    }

    public function readFileStream(array $entry)
    {
        $path = $this->getStorageSeparator() . $this->filledName($this->compactName($entry, $this->getStorageSeparator()));
        try {
            return $this->toStream($path, $this->storage->read($path));
        } catch (StorageException $ex) {
            throw new FilesException($this->getFlLang()->flCannotLoadFile($path), $ex->getCode(), $ex);
        }
    }

    public function copyFileStream(array $source, array $dest): bool
    {
        $srcPath = $this->getStorageSeparator() . $this->filledName($this->compactName($source, $this->getStorageSeparator()));
        $dstPath = $this->getStorageSeparator() . $this->filledName($this->compactName($dest, $this->getStorageSeparator()));
        try {
            if ($this->storage->exists($dstPath)) {
                return false;
            }
        } catch (StorageException $ex) {
            throw new FilesException($this->getFlLang()->flCannotCopyFile($srcPath, $dstPath), $ex->getCode(), $ex);
        }
        $stream = $this->readFileStream($source);
        if (!@rewind($stream)) {
            // @codeCoverageIgnoreStart
            throw new FilesException($this->getFlLang()->flCannotSeekFile($this->compactName($source, $this->getStorageSeparator())));
        }
        // @codeCoverageIgnoreEnd
        return $this->saveFileStream($dest, $stream,0, false);
    }

    public function moveFileStream(array $source, array $dest): bool
    {
        $srcPath = $this->getStorageSeparator() . $this->filledName($this->compactName($source, $this->getStorageSeparator()));
        $dstPath = $this->getStorageSeparator() . $this->filledName($this->compactName($dest, $this->getStorageSeparator()));
        try {
            $r1 = $this->copyFileStream($source, $dest);
            $r2 = $this->storage->remove($srcPath);
            return $r1 && $r2;
        } catch (StorageException $ex) {
            throw new FilesException($this->getFlLang()->flCannotMoveFile($srcPath, $dstPath), $ex->getCode(), $ex);
        }
    }

    /**
     * @param int<0, max> $mode
     * @throws FilesException
     */
    protected function checkSupportedModes(int $mode): void
    {
        if (!in_array($mode, [0, FILE_APPEND])) {
            throw new FilesException($this->getFlLang()->flBadMode($mode));
        }
    }

    protected function getStorageSeparator(): string
    {
        return DIRECTORY_SEPARATOR;
    }

    protected function filledName(string $path): string
    {
        $sepLen = mb_strlen($this->getStorageSeparator());
        $beginning = mb_substr($path, 0, $sepLen);
        return ($this->getStorageSeparator() == $beginning) ? mb_substr($path, $sepLen) : $path;
    }

    /**
     * @return string
     * @codeCoverageIgnore only when path fails
     */
    protected function noDirectoryDelimiterSet(): string
    {
        return $this->getFlLang()->flNoDirectoryDelimiterSet();
    }
}
