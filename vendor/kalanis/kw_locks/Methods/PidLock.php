<?php

namespace kalanis\kw_locks\Methods;


use kalanis\kw_locks\Interfaces\IPassedKey;
use kalanis\kw_locks\LockException;


/**
 * Class PidLock
 * @package kalanis\kw_locks\Methods
 * @codeCoverageIgnore accessing *nix calls
 */
class PidLock implements IPassedKey
{
    protected $tempPath = '';
    protected $specialKey = '';

    /**
     * @param string $tempPath
     * @throws LockException
     */
    public function __construct(string $tempPath)
    {
        if (\defined('PHP_OS_FAMILY') && in_array(PHP_OS_FAMILY, ['Windows', 'Unknown']) ) {
            throw new LockException('Unusable OS. Cannot use external programs to determine process ID.');
        }
        if (\DIRECTORY_SEPARATOR === '\\') {
            throw new LockException('Unusable OS. Cannot use external programs to determine process ID.');
        }
        $this->tempPath = $tempPath;
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

    public function setKey(string $key): void
    {
        $this->specialKey = $key;
    }

    public function has(): bool
    {
        if (file_exists($this->getLockFileName())) {
            $lockingPid = trim(file_get_contents($this->getLockFileName()));
            $otherOnes = explode(PHP_EOL, trim(`ps -e | awk '{print $1}'`));
            if (in_array($lockingPid, $otherOnes)) {
                return true;
            }
            throw new LockException('Locked by another!');
        }
        return false;
    }

    public function create(bool $force = false): bool
    {
        if (!$force && $this->has()) {
            return false;
        }
        $result = @file_put_contents($this->getLockFileName(), getmypid() . PHP_EOL);
        return (false !== $result);
    }

    public function delete(bool $force = false): bool
    {
        if (!$force && !$this->has()) {
            return true;
        }
        return @unlink($this->getLockFileName());
    }

    protected function getLockFileName()
    {
        return $this->tempPath . DIRECTORY_SEPARATOR . $this->specialKey . '.lock';
    }
}
