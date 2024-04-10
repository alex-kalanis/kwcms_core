<?php

namespace kalanis\kw_notify;


use ArrayAccess;
use kalanis\kw_notify\Interfaces\INotify;


/**
 * Class Stack
 * @package kalanis\kw_notify
 * Stack of notifications
 */
class Stack implements INotify
{
    /** @var ArrayAccess<string, array<string>> */
    protected ArrayAccess $storage;

    /**
     * @param ArrayAccess<string, array<string>> $storage Usually $_SESSION adapter
     */
    public function __construct(ArrayAccess $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Add content to stack
     * Check stack before
     * @param string $stackName
     * @param string $message
     * @throws NotifyException
     */
    public function add(string $stackName, string $message): void
    {
        if (!$this->check($stackName)) {
            $this->reset($stackName);
        }
        $this->addToStack($stackName, $message);
    }

    /**
     * Simply add content to stack
     * @param string $stackName
     * @param string $message
     * @throws NotifyException
     */
    protected function addToStack(string $stackName, string $message): void
    {
        $local = $this->get($stackName);
        $local[] = $message;
        $this->storage->offsetSet($stackName, $local);
    }

    public function check(string $stackName): bool
    {
        return $this->storage->offsetExists($stackName);
    }

    public function get(string $stackName): array
    {
        if (!$this->check($stackName)) {
            return [];
        }
        $data = $this->storage->offsetGet($stackName);
        return empty($data) ? [] : $data;
    }

    public function reset(string $stackName): void
    {
        $this->storage->offsetSet($stackName, []);
    }
}
