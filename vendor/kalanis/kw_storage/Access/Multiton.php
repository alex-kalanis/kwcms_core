<?php

namespace kalanis\kw_storage\Access;


use kalanis\kw_storage\Interfaces;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;
use kalanis\kw_storage\Traits\TLang;


/**
 * Class Multiton
 * @package kalanis\kw_storage\Access
 * Multiple storages via one access point; just use different params to get correct one
 */
class Multiton
{
    use TLang;

    /** @var Factory */
    protected $factory = null;
    /** @var array<string, IStorage> */
    protected $instances = [];

    public function __construct(?Interfaces\IStTranslations $lang = null)
    {
        $this->setStLang($lang);
        $this->factory = new Factory($this->getStLang());
    }

    /**
     * @param array<string|int, string|int|float|object|bool|array<string|int|float|object>>|string|object|int|bool|null $params
     * @param string|null $alias
     * @throws StorageException
     * @return IStorage
     */
    public function lookup($params, ?string $alias = null): IStorage
    {
        $key = $alias ?? $this->paramsToKey($params);
        if (!isset($this->instances[$key])) {
            $this->instances[$key] = $this->factory->getStorage($params);
        }

        return $this->instances[$key];
    }

    /**
     * @param array<string|int, string|int|float|object|bool|array<string|int|float|object>>|string|object|int|bool|null $params
     * @return string
     */
    public function paramsToKey($params): string
    {
        return md5(base64_encode(serialize($params)));
    }
}
