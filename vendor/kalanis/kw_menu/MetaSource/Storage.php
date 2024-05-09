<?php

namespace kalanis\kw_menu\MetaSource;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_menu\Interfaces\IMetaFileParser;
use kalanis\kw_menu\Interfaces\IMetaSource;
use kalanis\kw_menu\Interfaces\IMNTranslations;
use kalanis\kw_menu\Menu\Menu;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\Traits\TLang;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;


/**
 * Class Storage
 * @package kalanis\kw_menu\MetaSource
 * Data source is in passed storage
 */
class Storage implements IMetaSource
{
    use TLang;
    use TToString;

    /** @var string[] */
    protected array $key = [];
    protected IStorage $storage;
    protected IMetaFileParser $parser;

    /**
     * @param IStorage $storage
     * @param IMetaFileParser $parser
     * @param IMNTranslations|null $lang
     * @param string[] $metaKey
     */
    public function __construct(IStorage $storage, IMetaFileParser $parser, ?IMNTranslations $lang = null, array $metaKey = [])
    {
        $this->setMnLang($lang);
        $this->storage = $storage;
        $this->parser = $parser;
        $this->key = $metaKey;
    }

    public function setSource(array $metaSource): void
    {
        $this->key = $metaSource;
    }

    public function exists(): bool
    {
        try {
            return $this->storage->exists(Stuff::arrayToPath($this->key));
        } catch (StorageException | PathsException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function load(): Menu
    {
        try {
            $pt = Stuff::arrayToPath($this->key);
            return $this->parser->unpack($this->toString($pt, $this->storage->read($pt)));
        } catch (StorageException | PathsException | FilesException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function save(Menu $content): bool
    {
        try {
            return $this->storage->write(Stuff::arrayToPath($this->key), $this->parser->pack($content));
        } catch (StorageException | PathsException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
