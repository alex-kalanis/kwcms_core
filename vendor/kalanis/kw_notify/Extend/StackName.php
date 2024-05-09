<?php

namespace kalanis\kw_notify\Extend;


use kalanis\kw_notify\Interfaces\INotify;


/**
 * Class StackName
 * @package kalanis\kw_notify\Extend
 * Change key name in notifications
 */
class StackName implements INotify
{
    protected INotify $notify;
    protected string $prefix = '';
    protected string $suffix = '';

    public function __construct(INotify $storage, string $prefix = '', string $suffix = '')
    {
        $this->notify = $storage;
        $this->prefix = $prefix;
        $this->suffix = $suffix;
    }

    public function add(string $stackName, string $message): void
    {
        $this->notify->add($this->updateName($stackName), $message);
    }

    public function check(string $stackName): bool
    {
        return $this->notify->check($this->updateName($stackName));
    }

    public function get(string $stackName): array
    {
        return $this->notify->get($this->updateName($stackName));
    }

    public function reset(string $stackName): void
    {
        $this->notify->reset($this->updateName($stackName));
    }

    protected function updateName(string $stackName): string
    {
        return $this->prefix . $stackName . $this->suffix;
    }
}
