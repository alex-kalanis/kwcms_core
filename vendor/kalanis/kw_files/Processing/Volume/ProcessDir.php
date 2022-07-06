<?php

namespace kalanis\kw_files\Processing\Volume;


use Error;
use FilesystemIterator;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IFLTranslations;
use kalanis\kw_files\Interfaces\IProcessDirs;
use kalanis\kw_files\Translations;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileObject;


/**
 * Class ProcessDir
 * @package kalanis\kw_files\Processing\Volume
 * Process dirs in basic ways
 */
class ProcessDir implements IProcessDirs
{
    /** @var IFLTranslations */
    protected $lang = null;

    public function __construct(?IFLTranslations $lang = null)
    {
        $this->lang = $lang ?? new Translations();
    }

    public function createDir(string $entry, bool $deep = false): bool
    {
        try {
            return mkdir($entry, 0777, $deep);
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function readDir(string $entry, bool $loadRecursive = false): array
    {
        try {
            $iter = $loadRecursive
                ? new RecursiveIteratorIterator(new RecursiveDirectoryIterator($entry))
                : new FilesystemIterator($entry)
            ;
            return array_map([$this, 'namesOnly'], iterator_to_array($iter));
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function namesOnly(SplFileObject $file): string
    {
        return $file->getPath() . $file->getFilename();
    }

    public function copyDir(string $source, string $dest): bool
    {
        try {
            return $this->xcopy($source, $dest);
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

    public function moveDir(string $source, string $dest): bool
    {
        try {
            return rename($source, $dest);
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function deleteDir(string $entry, bool $deep = false): bool
    {
        try {
            return rmdir($entry);
        } catch (Error $ex) {
            throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
