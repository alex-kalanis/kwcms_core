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
    /** @var INotify|null */
    protected $notify = null;
    protected $prefix = '';
    protected $suffix = '';

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
