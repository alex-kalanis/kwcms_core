<?php

namespace kalanis\kw_input\Loaders;


use finfo;
use kalanis\kw_input\Entries;
use kalanis\kw_input\Interfaces\IEntry;


/**
 * Class CliEntry
 * @package kalanis\kw_input\Loaders
 * Load input arrays into normalized entries - CLI entries which could be also files
 */
class CliEntry extends ALoader
{
    protected static $basicPath = '';

    /**
     * @param string $path
     * @codeCoverageIgnore because must be set before test
     */
    public static function setBasicPath(string $path)
    {
        static::$basicPath = $path;
    }

    /**
     * Transform input values to something more reliable
     * @param string $source
     * @param array $array
     * @return Entries\Entry[]
     */
    public function loadVars(string $source, &$array): array
    {
        $result = [];
        $entries = new Entries\Entry();
        $fileEntries = new Entries\FileEntry();
        foreach ($array as $postedKey => $posted) {
            $fullPath = $this->checkFile($posted);
            if (!is_null($fullPath)) {
                $entry = clone $fileEntries;
                $entry->setEntry(IEntry::SOURCE_FILES, $postedKey);
                $entry->setFile(
                    $posted,
                    $fullPath,
                    $this->getType($fullPath),
                    UPLOAD_ERR_OK,
                    $this->getSize($fullPath)
                );
                $result[] = $entry;
            } else {
                $entry = clone $entries;
                $entry->setEntry($source, $postedKey, $posted);
                $result[] = $entry;
            }
        }
        return $result;
    }

    protected function checkFile($path): ?string
    {
        if (!is_string($path) || empty($path)) {
            return null;
        }
        $isFull = $path[0] == DIRECTORY_SEPARATOR;
        $known = realpath($isFull ? $path : static::$basicPath . DIRECTORY_SEPARATOR . $path );
        return (false === $known) ? null : $known ;
    }

    protected function getType($path): string
    {
        $fType = 'application/octet-stream';
        $fInfo = @new finfo(FILEINFO_MIME_TYPE);
        $fRes = @$fInfo->file($path);
        if (is_string($fRes) && !empty($fRes)) {
            $fType = $fRes;
        }
        return $fType;
    }

    protected function getSize($path): int
    {
        return filesize($path);
    }
}
