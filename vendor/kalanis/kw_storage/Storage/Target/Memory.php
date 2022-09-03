<?php

namespace kalanis\kw_storage\Storage\Target;


use kalanis\kw_storage\Interfaces\ITarget;
use kalanis\kw_storage\StorageException;
use Traversable;


/**
 * Class Memory
 * @package kalanis\kw_storage\Storage\Target
 * Store content onto memory - TEMPORARY
 */
class Memory implements ITarget
{
    use TOperations;

    /** @var array<string, mixed> */
    protected $data = [];

    public function check(string $key): bool
    {
        return true;
    }

    public function exists(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function load(string $key)
    {
        if (!$this->exists($key)) {
            throw new StorageException('Cannot read key');
        }
        return $this->data[$key];
    }

    public function save(string $key, $data, ?int $timeout = null): bool
    {
        $this->data[$key] = $data;
        return true;
    }

    public function remove(string $key): bool
    {
        if ($this->exists($key)) {
            unset($this->data[$key]);
        }
        return true;
    }

    public function lookup(string $path): Traversable
    {
        $keyLen = mb_strlen($path);
        foreach ($this->data as $file => $entry) {
            if (0 === mb_strpos($file, $path)) {
                yield mb_substr($file, $keyLen);
            }
        }
    }

    public function increment(string $key, int $step = 1): bool
    {
        if ($this->exists($key)) {
            $number = intval($this->load($key)) + $step;
        } else {
            $number = 1;
        }
        return $this->save($key, $number);
    }

    public function decrement(string $key, int $step = 1): bool
    {
        if ($this->exists($key)) {
            $number = intval($this->load($key)) - $step;
        } else {
            $number = 0;
        }
        return $this->save($key, $number);
    }
}
