<?php

namespace kalanis\kw_locks\Methods;


use kalanis\kw_locks\Interfaces\IKLTranslations;
use kalanis\kw_locks\Interfaces\ILock;
use kalanis\kw_locks\LockException;
use kalanis\kw_locks\Traits\TLang;
use kalanis\kw_paths\Stuff;


/**
 * Class FileLock
 * @package kalanis\kw_locks\Methods
 * Lock some target
 * Uses low-level volume operations
 * @codeCoverageIgnore accessing local volume
 */
class FileLock implements ILock
{
    use TLang;

    protected string $lockFilename = '';
    /** @var resource|null */
    protected $handle = null;

    /**
     * @param string $lockFilename
     * @param IKLTranslations|null $lang
     * @throws LockException
     */
    public function __construct(string $lockFilename, ?IKLTranslations $lang = null)
    {
        $this->setKlLang($lang);
        $path = Stuff::directory($lockFilename);
        $this->accessDir($path);
        if ((!is_file($lockFilename) && !is_writable($path)) || (is_file($lockFilename) && !is_writable($lockFilename))) {
            throw new LockException($this->getKlLang()->iklCannotUseFile($lockFilename));
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
                throw new LockException($this->getKlLang()->iklCannotUsePath($path));
            }
        }
    }

    public function has(): bool
    {
        if (file_exists($this->lockFilename)) {
            if (!empty($this->handle)) {
                return true;
            }
            throw new LockException($this->getKlLang()->iklLockedByOther());
        }
        return false;
    }

    public function create(bool $force = false): bool
    {
        if ($this->has()) {
            return false;
        }

        $handle = @fopen($this->lockFilename, 'c');
        if (false === $handle) {
            throw new LockException($this->getKlLang()->iklCannotOpenFile($this->lockFilename));
        }
        $this->handle = $handle;
        $result = flock($this->handle, LOCK_EX | LOCK_NB);
        if (false === $result) {
            fclose($this->handle);
        }

        if (true === $result) {
            $writeStatus = @fwrite($this->handle, strval(getmypid()));
            if (false === $writeStatus) {
                fclose($this->handle);
                return false;
            }
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
