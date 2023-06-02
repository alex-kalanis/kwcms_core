<?php

namespace kalanis\kw_files\Processing\Storage\Files;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_files\Traits\TLang;
use kalanis\kw_files\Traits\TStreamToPos;
use kalanis\kw_files\Traits\TToStream;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Extras\TPathTransform;
use kalanis\kw_storage\Interfaces\IPassDirs;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class AFiles
 * @package kalanis\kw_files\Processing\Storage\Files
 * Process files in storages - deffer when you can access them directly or must be a middleman there
 */
abstract class AFiles implements IProcessFiles
{
    use TLang;
    use TPathTransform;
    use TStreamToPos;
    use TToStream;

    /** @var IStorage|IPassDirs */
    protected $storage = null;

    public function saveFile(array $targetName, $content, ?int $offset = null): bool
    {
        $path = $this->getStorageSeparator() . $this->filledName($this->compactName($targetName, $this->getStorageSeparator()));
        try {
            $dstArr = new ArrayPath();
            $dstArr->setArray($targetName);
            $tgt = $this->compactName($dstArr->getArrayDirectory(), $this->getStorageSeparator());
            if (!empty($tgt) && !$this->storage->exists($this->getStorageSeparator() . $tgt)) {
                // parent dir
                throw new FilesException($this->getLang()->flCannotSaveFile($path));
            }

            $added = $this->toStream($path, $content);
            if (!is_null($offset)) {
                // put it somewhere, left the rest intact
                if ($this->storage->exists($path)) {
                    $target = $this->toStream($path, $this->storage->read($path));
                } else {
                    $target = $this->toStream('', '');
                }
                return $this->storage->write($path, $this->addStreamToPosition($target, $added, $offset));
            } else {
                return $this->storage->write($path, $content);
            }
        } catch (StorageException $ex) {
            throw new FilesException($this->getLang()->flCannotSaveFile($path), $ex->getCode(), $ex);
        }
    }

    public function readFile(array $entry, ?int $offset = null, ?int $length = null)
    {
        $path = $this->getStorageSeparator() . $this->filledName($this->compactName($entry, $this->getStorageSeparator()));
        try {
            $content = $this->storage->read($path);
            if (false === $content) {
                throw new FilesException($this->getLang()->flCannotLoadFile($path));
            } elseif (is_resource($content)) {
                if (!is_null($length) || !is_null($offset)) {
                    $stream = fopen('php://temp', 'rb+');
                    rewind($content);
                    if (false === $stream) {
                        // @codeCoverageIgnoreStart
                        throw new FilesException($this->getLang()->flCannotLoadFile($path));
                    }
                    // @codeCoverageIgnoreEnd
                    if (false === stream_copy_to_stream($content, $stream, (is_null($length) ? -1 : $length), intval($offset))) {
                        // @codeCoverageIgnoreStart
                        throw new FilesException($this->getLang()->flCannotGetFilePart($path));
                    }
                    // @codeCoverageIgnoreEnd
                    return $stream;
                } else {
                    return $content;
                }
            } else {
                // shit with substr... that needed undefined params was from some java dude?!
                if (!is_null($length)) {
                    return mb_substr(strval($content), intval($offset), $length);
                }
                if (!is_null($offset)) {
                    return mb_substr(strval($content), $offset);
                }
                return strval($content);
            }
        } catch (StorageException $ex) {
            throw new FilesException($this->getLang()->flCannotLoadFile($path), $ex->getCode(), $ex);
        }
    }

    public function deleteFile(array $entry): bool
    {
        $path = $this->getStorageSeparator() . $this->filledName($this->compactName($entry, $this->getStorageSeparator()));
        try {
            return $this->storage->remove($path);
        } catch (StorageException $ex) {
            throw new FilesException($this->getLang()->flCannotRemoveFile($path), $ex->getCode(), $ex);
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
}
