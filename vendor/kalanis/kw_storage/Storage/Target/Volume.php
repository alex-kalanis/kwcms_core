<?php

namespace kalanis\kw_storage\Storage\Target;


use kalanis\kw_storage\Extras\TRemoveCycle;
use kalanis\kw_storage\Interfaces\IPassDirs;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;
use Traversable;


/**
 * Class Volume
 * @package kalanis\kw_storage\Storage\Target
 * Store content onto volume
 */
class Volume implements IStorage, IPassDirs
{
    use TOperations;
    use TRemoveCycle;

    public function check(string $key): bool
    {
        $sepPos = mb_strrpos($key, DIRECTORY_SEPARATOR);
        $path = (false === $sepPos) ? substr($key, 0) : substr($key, 0, intval($sepPos));
        if (!is_dir($path)) {
            if (file_exists($path)) {
                unlink($path);
            }
            return mkdir($path, 0777);
        }
        return true;
    }

    public function exists(string $key): bool
    {
        return file_exists($key);
    }

    public function isDir(string $key): bool
    {
        return is_dir($key);
    }

    public function mkDir(string $key, bool $recursive = false): bool
    {
        return mkdir($key, 0777, $recursive);
    }

    public function rmDir(string $key, bool $recursive = false): bool
    {
        return $recursive ? $this->removeCycle($key) && rmdir($key) : rmdir($key);
    }

    public function load(string $key)
    {
        $content = @file_get_contents($key);
        if (false === $content) {
            throw new StorageException('Cannot read file');
        }
        return $content;
    }

    public function save(string $key, $data, ?int $timeout = null): bool
    {
        return (false !== @file_put_contents($key, strval($data)));
    }

    public function copy(string $source, string $dest): bool
    {
        return $this->xcopy($source, $dest);
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

    public function move(string $source, string $dest): bool
    {
        return @rename($source, $dest);
    }

    public function remove(string $key): bool
    {
        return @unlink($key);
    }

    public function lookup(string $path): Traversable
    {
        $real = realpath($path);
        if (false === $real) {
            return;
        }
        $files = scandir($real);
        if (!empty($files)) {
            foreach ($files as $file) {
                yield $file;
            }
        }
    }

    public function increment(string $key, int $step = 1): bool
    {
        try {
            $number = intval($this->load($key)) + $step;
        } catch (StorageException $ex) {
            // no file
            $number = 1;
        }
        $this->remove($key); // hanging pointers
        return $this->save($key, $number);
    }

    public function decrement(string $key, int $step = 1): bool
    {
        try {
            $number = intval($this->load($key)) - $step;
        } catch (StorageException $ex) {
            // no file
            $number = 0;
        }
        $this->remove($key); // hanging pointers
        return $this->save($key, $number);
    }
}
