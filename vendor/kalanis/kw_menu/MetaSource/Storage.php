<?php

namespace kalanis\kw_menu\MetaSource;


use kalanis\kw_menu\Interfaces\IMetaFileParser;
use kalanis\kw_menu\Interfaces\IMetaSource;
use kalanis\kw_menu\Menu\Menu;
use kalanis\kw_menu\MenuException;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class Storage
 * @package kalanis\kw_menu\MetaSource
 * Data source is in passed storage
 */
class Storage implements IMetaSource
{
    /** @var string */
    protected $key = '';
    /** @var IStorage */
    protected $storage = null;
    /** @var FileParser */
    protected $parser = null;

    public function __construct(IStorage $storage, IMetaFileParser $parser, string $metaKey)
    {
        $this->storage = $storage;
        $this->parser = $parser;
        $this->key = $metaKey;
    }

    public function setSource(string $metaSource): void
    {
        $this->key = $metaSource;
    }

    public function exists(): bool
    {
        try {
            return $this->storage->exists($this->key);
        } catch (StorageException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function load(): Menu
    {
        try {
            return $this->parser->unpack($this->storage->read($this->key));
        } catch (StorageException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function save(Menu $content): bool
    {
        try {
            return $this->storage->write($this->key, $this->parser->pack($content));
        } catch (StorageException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
