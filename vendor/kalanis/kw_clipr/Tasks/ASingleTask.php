<?php

namespace kalanis\kw_clipr\Tasks;


use kalanis\kw_locks\Interfaces\ILock;
use kalanis\kw_locks\Interfaces\IPassedKey;
use kalanis\kw_locks\LockException;
use kalanis\kw_locks\Methods\PidLock;


/**
 * Class ASingleTask
 * @package kalanis\kw_clipr\Tasks
 * @property bool singleInstance
 */
abstract class ASingleTask extends ATask
{
    protected $lock = null;

    public function __construct(?ILock $lock = null)
    {
        $this->lock = empty($lock) ? $this->getPresetLock() : $lock;
        if ($lock instanceof IPassedKey) {
            $lock->setKey(str_replace('/', ':', get_class($this)) . ILock::LOCK_FILE);
        } elseif (method_exists($lock, 'setClass')) {
            $lock->setClass($this);
        }
        // temp dir path must go via lock's constructor
        // when it comes via IStorage (StorageLock), it's possible to connect it into Redis or Memcache and then that path might not be necessary
    }

    protected function getPresetLock(): ILock
    {
        return new PidLock($this->getTempPath());
    }

    protected function getTempPath(): string
    {
        return '/tmp';
    }

    protected function startup(): void
    {
        parent::startup();
        $this->params->addParam('singleInstance', 'single-instance', null, false, 's', 'Call only single instance');

        $this->checkSingleInstance();
    }

    protected function checkSingleInstance()
    {
        try {
            if ($this->isSingleInstance() && $this->isFileLocked()) {
                // check if exists another instance
                die('One script instance is already running!');
                // create own lock file
            }
        } catch (LockException $ex) {
            die('Locked by another user. Cannot unlock here.');
        }
    }

    protected function isSingleInstance(): bool
    {
        return (true == $this->singleInstance);
    }

    /**
     * @return bool
     * @throws LockException
     */
    protected function isFileLocked(): bool
    {
        try {
            if (!$this->lock->has()) {
                $this->lock->create();
            }
            return true;
        } catch (LockException $ex) {
            $this->writeLn("Removing stale lock file.");
            $this->lock->delete(true);
            return false;
        }
    }
}
