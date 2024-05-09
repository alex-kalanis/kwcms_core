<?php

namespace kalanis\kw_storage_session;


use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;
use SessionHandlerInterface;


/**
 * Class StorageSession
 * @package kalanis\kw_storage
 * Main storage class
 *

 * Use:
$storage = \kalanis\kw_storage\Helper::initCache();
$storage->init(new Redis());
$handler = new StorageSession($storage);
session_set_save_handler($handler, true);
session_start();

 */
class StorageSession implements SessionHandlerInterface
{
    protected IStorage $storage;
    protected int $ttl = 1800; // 30 minutes default

    public function __construct(IStorage $storage, ?int $ttl = null)
    {
        $this->storage = $storage;
        $this->ttl = is_null($ttl) ? $this->ttl : $ttl ;
    }

    /**
     * @param string $savePath
     * @param string $sessionName
     * @return bool
     */
    public function open($savePath, $sessionName): bool
    {
        // No action necessary because connection is injected
        // in constructor and arguments are not applicable.
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    /**
     * @param string $id
     * @throws StorageException
     * @return string
     */
    #[ReturnTypeWillChange]
    public function read($id)
    {
        return strval($this->storage->read($id));
    }

    /**
     * @param string $id
     * @param string $data
     * @throws StorageException
     * @return bool
     */
    public function write($id, $data): bool
    {
        return $this->storage->write($id, $data, $this->ttl);
    }

    /**
     * @param string $id
     * @throws StorageException
     * @return bool
     */
    public function destroy($id): bool
    {
        return $this->storage->remove($id);
    }

    public function gc($maxLifetime)
    {
        // no action necessary because using storage own capabilities - redis and memcache has their own expire ttl
        // !!! beware on Volume - it does not delete sessions !!!
    }
}
