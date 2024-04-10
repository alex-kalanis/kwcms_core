<?php

namespace kalanis\kw_storage\Storage\Target;


use kalanis\kw_storage\Interfaces\IStTranslations;
use kalanis\kw_storage\Interfaces\ITargetFlat;
use kalanis\kw_storage\StorageException;
use kalanis\kw_storage\Traits\TLang;
use Traversable;


/**
 * Class Memory
 * @package kalanis\kw_storage\Storage\Target
 * Store content onto memory - TEMPORARY
 */
class Memory implements ITargetFlat
{
    use TOperations;
    use TLang;

    /** @var array<string, string> */
    protected array $data = [];

    public function __construct(?IStTranslations $lang = null)
    {
        $this->setStLang($lang);
    }

    public function check(string $key): bool
    {
        return true;
    }

    public function exists(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function load(string $key): string
    {
        if (!$this->exists($key)) {
            throw new StorageException($this->getStLang()->stCannotReadKey());
        }
        return $this->data[$key];
    }

    public function save(string $key, string $data, ?int $timeout = null): bool
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
            if (boolval($keyLen) && (0 === mb_strpos($file, $path))) {
                yield mb_substr($file, $keyLen);
            } elseif (!boolval($keyLen)) {
                yield $file;
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
        return $this->save($key, strval($number));
    }

    public function decrement(string $key, int $step = 1): bool
    {
        if ($this->exists($key)) {
            $number = intval($this->load($key)) - $step;
        } else {
            $number = 0;
        }
        return $this->save($key, strval($number));
    }
}
