<?php

namespace kalanis\kw_storage\Extras;


/**
 * Trait TVolumeCopy
 * @package kalanis\kw_storage\Extras
 * Copy dirs - deep
 */
trait TVolumeCopy
{
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
        // check if source exists
        if (!file_exists($source)) {
            return false;
        }

        $sourceHash = $this->hashDirectory($source);

        // Check for symlinks
        if (is_link($source)) {
            // @codeCoverageIgnoreStart
            $data = readlink($source);
            if (false === $data) {
                return false;
            }
            return symlink($data, $dest);
        }
        // @codeCoverageIgnoreEnd

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
        while (false !== ($entry = $dir->read())) {
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
}
