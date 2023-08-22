<?php

namespace kalanis\kw_storage;


use kalanis\kw_storage\Interfaces\IStTranslations;


/**
 * Class Translations
 * @package kalanis\kw_storage
 */
class Translations implements IStTranslations
{
    public function stCannotReadKey(): string
    {
        return 'Cannot read key';
    }

    public function stCannotReadFile(): string
    {
        return 'Cannot read file';
    }

    /**
     * @return string
     * @codeCoverageIgnore VolumeStream
     */
    public function stCannotOpenFile(): string
    {
        return 'Cannot open file';
    }

    /**
     * @return string
     * @codeCoverageIgnore VolumeStream
     */
    public function stCannotSaveFile(): string
    {
        return 'Cannot save file';
    }

    /**
     * @return string
     * @codeCoverageIgnore VolumeStream
     */
    public function stCannotSeekFile(): string
    {
        return 'Cannot seek in file';
    }

    /**
     * @return string
     * @codeCoverageIgnore VolumeStream
     */
    public function stCannotCloseFile(): string
    {
        return 'Cannot close opened file';
    }

    public function stStorageNotInitialized(): string
    {
        return 'Storage not initialized';
    }

    public function stPathNotFound(): string
    {
        return 'Path in storage not found.';
    }

    public function stConfigurationUnavailable(): string
    {
        return 'This configuration is not available.';
    }
}
