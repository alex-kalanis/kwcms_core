<?php

namespace kalanis\kw_storage\Storage\Target;


use kalanis\kw_storage\Interfaces\ITargetFlat;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Traversable;


/**
 * Class VolumeTargetFlat
 * @package kalanis\kw_storage\Storage\Target
 * Lookup deeper than into selected node/directory; use as flat structure
 */
class VolumeTargetFlat extends Volume implements ITargetFlat
{
    public function lookup(string $path): Traversable
    {
        $real = realpath($path);
        if (false === $real) {
            return;
        }

        $len = mb_strlen($real . DIRECTORY_SEPARATOR);
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($real)) as $file) {
            /** @var SplFileInfo $file */
            $currentPath = mb_substr(strval($file), $len);
            if ('..' == $file->getFilename()) {
                // parent skip
            } elseif ('.' == $file->getFilename()) {
                // current process
                $lastSlash = mb_strrpos($file->getRealPath(), DIRECTORY_SEPARATOR);
                if (false === $lastSlash || '.' == $currentPath) {
                    yield '';
                } else {
                    yield DIRECTORY_SEPARATOR . mb_substr($file->getRealPath(), $len);
                }
            } else {
                yield DIRECTORY_SEPARATOR . $currentPath;
            }
        }
    }
}
