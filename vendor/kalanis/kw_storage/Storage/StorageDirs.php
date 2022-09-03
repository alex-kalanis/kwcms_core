<?php

namespace kalanis\kw_storage\Storage;


use kalanis\kw_storage\Interfaces\IPassDirs;
use kalanis\kw_storage\Interfaces\ITargetVolume;


/**
 * Class StorageDirs
 * @package kalanis\kw_storage
 * Extended storage class
 */
class StorageDirs extends Storage implements IPassDirs
{
    /** @var ITargetVolume */
    protected $target = null;

    public function isDir(string $key): bool
    {
        return $this->target->isDir($this->key->fromSharedKey($key));
    }

    public function isFile(string $key): bool
    {
        return $this->target->isFile($this->key->fromSharedKey($key));
    }

    public function mkDir(string $key, bool $recursive = false): bool
    {
        return $this->target->mkDir($this->key->fromSharedKey($key), $recursive);
    }

    public function rmDir(string $key, bool $recursive = false): bool
    {
        return $this->target->rmDir($this->key->fromSharedKey($key), $recursive);
    }

    public function copy(string $source, string $dest): bool
    {
        return $this->target->copy($this->key->fromSharedKey($source), $this->key->fromSharedKey($dest));
    }

    public function move(string $source, string $dest): bool
    {
        return $this->target->move($this->key->fromSharedKey($source), $this->key->fromSharedKey($dest));
    }

    public function size(string $key): ?int
    {
        return $this->target->size($this->key->fromSharedKey($key));
    }

    public function created(string $key): ?int
    {
        return $this->target->created($this->key->fromSharedKey($key));
    }
}
