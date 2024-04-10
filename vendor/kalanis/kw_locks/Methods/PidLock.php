<?php

namespace kalanis\kw_locks\Methods;


use kalanis\kw_locks\Interfaces\IKLTranslations;
use kalanis\kw_locks\Interfaces\IPassedKey;
use kalanis\kw_locks\LockException;
use kalanis\kw_locks\Traits\TLang;


/**
 * Class PidLock
 * @package kalanis\kw_locks\Methods
 * @codeCoverageIgnore accessing *nix calls
 */
class PidLock implements IPassedKey
{
    use TLang;

    protected string $tempPath = '';
    protected string $specialKey = '';

    /**
     * @param string $tempPath
     * @param IKLTranslations|null $lang
     * @throws LockException
     */
    public function __construct(string $tempPath, ?IKLTranslations $lang = null)
    {
        $this->setKlLang($lang);
        if (\defined('PHP_OS_FAMILY') && in_array(PHP_OS_FAMILY, ['Windows', 'Unknown']) ) {
            throw new LockException($this->getKlLang()->iklCannotUseOS());
        }
        if (\DIRECTORY_SEPARATOR === '\\') {
            throw new LockException($this->getKlLang()->iklCannotUseOS());
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
            $lockingPid = trim(strval(file_get_contents($this->getLockFileName())));
            $otherOnes = explode(PHP_EOL, trim(`ps -e | awk '{print $1}'`));
            if (in_array($lockingPid, $otherOnes)) {
                return true;
            }
            throw new LockException($this->getKlLang()->iklLockedByOther());
        }
        return false;
    }

    public function create(bool $force = false): bool
    {
        if (!$force && $this->has()) {
            return false;
        }
        $result = @file_put_contents($this->getLockFileName(), strval(getmypid()) . PHP_EOL);
        return (false !== $result);
    }

    public function delete(bool $force = false): bool
    {
        if (!$force && !$this->has()) {
            return true;
        }
        return @unlink($this->getLockFileName());
    }

    protected function getLockFileName(): string
    {
        return $this->tempPath . DIRECTORY_SEPARATOR . $this->specialKey . '.lock';
    }
}
