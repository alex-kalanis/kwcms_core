<?php

namespace KWCMS\modules\Files\Lib;


use Error;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Files\FilesException;
use KWCMS\modules\Files\Interfaces\IProcessDirs;


/**
 * Class ProcessDir
 * @package KWCMS\modules\Files\Lib
 * Process dirs in basic ways
 */
class ProcessDir implements IProcessDirs
{
    protected $sourcePath = '';
    protected $currentDir = '';

    public function __construct(string $sourcePath, string $currentDir)
    {
        $this->sourcePath = $sourcePath;
        $this->currentDir = $currentDir;
    }

    public function createDir(string $entry): bool
    {
        try {
            return mkdir($this->sourcePath . $this->currentDir . Stuff::filename($entry));
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function copyDir(string $entry, string $to): bool
    {
        $fileName = Stuff::filename($entry);
        try {
            return $this->xcopy(
                $this->sourcePath . $this->currentDir . $entry,
                $this->sourcePath . strval($to) . DIRECTORY_SEPARATOR . $fileName
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * Copy a file, or recursively copy a folder and its contents
     * @param string $source Source path
     * @param string $dest Destination path
     * @param int $permissions New folder creation permissions
     * @return      bool     Returns true on success, false on failure
     * @version     1.0.1
     * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
     * @link        https://stackoverflow.com/questions/2050859/copy-entire-contents-of-a-directory-to-another-using-php
     * @author      Aidan Lister <aidan@php.net>
     */
    protected function xcopy(string $source, string $dest, int $permissions = 0755): bool
    {
        $sourceHash = $this->hashDirectory($source);
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest, $permissions);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if (in_array($entry, ['.', '..'])) {
                continue;
            }

            // Deep copy directories
            if ($sourceHash != $this->hashDirectory($source . DIRECTORY_SEPARATOR . $entry)) {
                $this->xcopy($source . DIRECTORY_SEPARATOR . $entry, $dest . DIRECTORY_SEPARATOR . $entry, $permissions);
            }
        }

        // Clean up
        $dir->close();
        return true;
    }

    /**
     * In case of coping a directory inside itself, there is a need to hash check the directory otherwise and infinite loop of coping is generated
     * @param string $directory
     * @return string|null
     */
    protected function hashDirectory(string $directory): ?string
    {
        if (!is_dir($directory)) {
            return null;
        }

        $files = [];
        $dir = dir($directory);

        while (false !== ($file = $dir->read())) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            if (is_dir($directory . DIRECTORY_SEPARATOR . $file)) {
                $files[] = $this->hashDirectory($directory . DIRECTORY_SEPARATOR . $file);
            } else {
                $files[] = md5_file($directory . DIRECTORY_SEPARATOR . $file);
            }
        }

        $dir->close();

        return md5(implode('', $files));
    }

    public function moveDir(string $entry, string $to): bool
    {
        $fileName = Stuff::filename($entry);
        try {
            return rename(
                $this->sourcePath . $this->currentDir . $entry,
                $this->sourcePath . strval($to) . DIRECTORY_SEPARATOR . $fileName
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function renameDir(string $entry, string $to): bool
    {
        try {
            return rename(
                $this->sourcePath . $this->currentDir . $entry,
                $this->sourcePath . $this->currentDir . $to
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function deleteDir(string $entry): bool
    {
        try {
            return rmdir(
                $this->sourcePath . $this->currentDir . $entry
            );
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
