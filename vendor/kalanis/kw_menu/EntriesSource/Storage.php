<?php

namespace kalanis\kw_menu\EntriesSource;


use kalanis\kw_menu\Interfaces\IEntriesSource;
use kalanis\kw_menu\MenuException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_storage\Storage\Storage as XStorage;
use kalanis\kw_storage\StorageException;
use Traversable;


/**
 * Class Storage
 * @package kalanis\kw_menu\EntriesSource
 * Entries source is in passed storage
 */
class Storage implements IEntriesSource
{
    use TFilterHtml;

    /** @var XStorage */
    protected $storage = null;

    public function __construct(XStorage $storage)
    {
        $this->storage = $storage;
    }

    public function getFiles(string $dir): Traversable
    {
        try {
            foreach ($this->storage->lookup($dir) as $item) {
                if ($this->filterExt(Stuff::fileExt($item))) {
                    yield $item;
                }
            }
            // @codeCoverageIgnoreStart
        } catch (StorageException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
        // @codeCoverageIgnoreEnd
    }
}
