<?php

namespace kalanis\kw_extras;


use kalanis\kw_paths\Stuff;


/**
 * Class Lock
 * @package kalanis\kw_extras
 * Lock some target
 * Uses low-level volume operations
 */
class Lock
{
    protected $lockFilename = '';
    protected $handle = null;

    /**
     * @param string $lockFilename
     * @throws ExtrasException
     */
    public function __construct(string $lockFilename)
    {
        $path = Stuff::directory($lockFilename);
        $this->accessDir($path);
        if ((!is_file($lockFilename) && !is_writable($path)) || (is_file($lockFilename) && !is_writable($lockFilename))) {
            throw new ExtrasException('Cannot use: ' . $lockFilename . ' as lock file. Path is not writable.');
        }
        $this->lockFilename = $lockFilename;
    }

    /**
     * @param string $path
     * @throws ExtrasException
     */
    protected function accessDir(string $path): void
    {
        if (!is_dir($path)) {
            if (mkdir($path, 0777, true)) {
                chmod($path, 0777);
            } else {
                throw new ExtrasException('Cannot use: ' . $path . ' to lock. Path not found and cannot be created.');
            }
        }
    }

    public function getLockFileName()
    {
        return $this->lockFilename;
    }

    /**
     * Get exclusive lock
     * @return bool
     * @throws ExtrasException
     */
    public function exclusiveLock(): bool
    {
        $this->handle = @fopen($this->lockFilename, 'c');
        if (false === $this->handle) {
            throw new ExtrasException('Could not open lock file: '. $this->lockFilename);
        }
        $result = flock($this->handle, LOCK_EX | LOCK_NB);
        if (false === $result) {
            fclose($this->handle);
        }

        if (true === $result) {
            @file_put_contents($this->lockFilename, getmypid());
        }

        return $result;
    }

    /**
     * Close lock
     * @return bool
     */
    public function closeLock(): bool
    {
        if (is_resource($this->handle)) {
            $result = flock($this->handle, LOCK_UN);
            fclose($this->handle);
        } else {
            $result = true;
        }
        return $result;
    }

    public function isMyLock(): bool
    {
        return is_file($this->lockFilename) && is_resource($this->handle);
    }

    public function isAnotherLock(): bool
    {
        return is_file($this->lockFilename) && empty($this->handle);
    }

    public function removeLockfile(): void
    {
        @unlink($this->lockFilename);
    }

    public function __destruct()
    {
        $this->closeLock();
    }
}
