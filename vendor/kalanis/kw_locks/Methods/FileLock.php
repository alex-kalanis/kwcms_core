<?php

namespace kalanis\kw_locks\Methods;


use kalanis\kw_locks\Interfaces\ILock;
use kalanis\kw_locks\LockException;
use kalanis\kw_paths\Stuff;


/**
 * Class FileLock
 * @package kalanis\kw_locks\Methods
 * Lock some target
 * Uses low-level volume operations
 */
class FileLock implements ILock
{
    protected $lockFilename = '';
    protected $handle = null;

    /**
     * @param string $lockFilename
     * @throws LockException
     */
    public function __construct(string $lockFilename)
    {
        $path = Stuff::directory($lockFilename);
        $this->accessDir($path);
        if ((!is_file($lockFilename) && !is_writable($path)) || (is_file($lockFilename) && !is_writable($lockFilename))) {
            throw new LockException('Cannot use: ' . $lockFilename . ' as lock file. Path is not writable.');
        }
        $this->lockFilename = $lockFilename;
    }

    public function __destruct()
    {
        try {
            $this->delete();
        } catch (LockException $ex) {
            // do nothing instead of
            // register_shutdown_function([$this, 'delete']);
        }
    }

    /**
     * @param string $path
     * @throws LockException
     */
    protected function accessDir(string $path): void
    {
        if (!is_dir($path)) {
            if (mkdir($path, 0777, true)) {
                chmod($path, 0777);
            } else {
                throw new LockException('Cannot use: ' . $path . ' to lock. Path not found and cannot be created.');
            }
        }
    }

    public function has(): bool
    {
        if (file_exists($this->lockFilename)) {
            if (!empty($this->handle)) {
                return true;
            }
            throw new LockException('Locked by another!');
        }
        return false;
    }

    public function create(bool $force = false): bool
    {
        if ($this->has()) {
            return false;
        }

        $this->handle = @fopen($this->lockFilename, 'c');
        if (false === $this->handle) {
            throw new LockException('Could not open lock file: '. $this->lockFilename);
        }
        $result = flock($this->handle, LOCK_EX | LOCK_NB);
        if (false === $result) {
            fclose($this->handle);
        }

        if (true === $result) {
            @fwrite($this->handle, getmypid());
        }

        return $result;
    }

    public function delete(bool $force = false): bool
    {
        if (!$this->has()) {
            return true;
        }
        if (is_resource($this->handle)) {
            $resultLock = flock($this->handle, LOCK_UN);
            $resultHandle = fclose($this->handle);
            $resultRemove = @unlink($this->lockFilename);
            return ($resultLock && $resultHandle && $resultRemove);
        } else {
            return false;
        }
    }
}
