<?php

namespace kalanis\kw_storage\Storage\Key;


use kalanis\kw_storage\Interfaces\IKey;
use kalanis\kw_storage\Interfaces\IStTranslations;
use kalanis\kw_storage\StorageException;
use kalanis\kw_storage\Traits\TLang;


/**
 * Class ArrayDirKey
 * @package kalanis\kw_storage\Key
 * Added prefix path - in form of array
 * Check if dir exists
 */
class ArrayDirKey implements IKey
{
    use TLang;

    /** @var string */
    protected $prefix = '';

    /**
     * @param string[] $prefix
     * @param IStTranslations|null $lang
     * @throws StorageException
     */
    public function __construct(array $prefix, ?IStTranslations $lang = null)
    {
        $this->setStLang($lang);
        if ($pt = realpath(implode(DIRECTORY_SEPARATOR, $prefix))) {
            $this->prefix = strval($pt);
        } else {
            throw new StorageException($this->getStLang()->stPathNotFound());
        }
    }

    public function fromSharedKey(string $key): string
    {
        return $this->prefix . $key;
    }
}
