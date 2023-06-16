<?php

namespace kalanis\kw_storage\Storage\Target;


use kalanis\kw_storage\StorageException;


/**
 * Class VolumeStream
 * @package kalanis\kw_storage\Storage\Target
 * Store content onto volume - streams
 */
class VolumeStream extends Volume
{
    /**
     * @param string $key
     * @throws StorageException
     * @return resource
     */
    public function load(string $key)
    {
        $content = @fopen($key, 'rb');
        $data = @fopen('php://temp', 'r+b');
        if ((false === $content) || (false === $data)) {
            // @codeCoverageIgnoreStart
            throw new StorageException($this->getStLang()->stCannotReadFile());
        }
        // @codeCoverageIgnoreEnd
        if (false === @stream_copy_to_stream($content, $data)) {
            // @codeCoverageIgnoreStart
            throw new StorageException($this->getStLang()->stCannotReadFile());
        }
        if (false === @fclose($content)) {
            // @codeCoverageIgnoreStart
            throw new StorageException($this->getStLang()->stCannotCloseFile());
        }
        // @codeCoverageIgnoreEnd
        return $data;
    }

    /**
     * @param string $key
     * @param resource $data
     * @param int|null $timeout
     * @throws StorageException
     * @return bool
     */
    public function save(string $key, $data, ?int $timeout = null): bool
    {
        $content = @fopen($key, 'wb');
        if (false === $content) {
            // @codeCoverageIgnoreStart
            throw new StorageException($this->getStLang()->stCannotOpenFile());
        }
        // @codeCoverageIgnoreEnd
        if (false === @stream_copy_to_stream($data, $content, -1, 0)) {
            // @codeCoverageIgnoreStart
            throw new StorageException($this->getStLang()->stCannotSaveFile());
        }
        // @codeCoverageIgnoreEnd
        if (-1 === @fseek($content, 0)) {
            // @codeCoverageIgnoreStart
            throw new StorageException($this->getStLang()->stCannotSeekFile());
        }
        // @codeCoverageIgnoreEnd
        if (false === @fclose($content)) {
            // @codeCoverageIgnoreStart
            throw new StorageException($this->getStLang()->stCannotCloseFile());
        }
        // @codeCoverageIgnoreEnd
        return true;
    }

    public function increment(string $key, int $step = 1): bool
    {
        try {
            $number = intval(Volume::load($key)) + $step;
        } catch (StorageException $ex) {
            // no file
            $number = 1;
        }
        $this->remove($key); // hanging pointers
        return Volume::save($key, $number);
    }

    public function decrement(string $key, int $step = 1): bool
    {
        try {
            $number = intval(Volume::load($key)) - $step;
        } catch (StorageException $ex) {
            // no file
            $number = 0;
        }
        $this->remove($key); // hanging pointers
        return Volume::save($key, $number);
    }
}
