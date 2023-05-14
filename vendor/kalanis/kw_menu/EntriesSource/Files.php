<?php

namespace kalanis\kw_menu\EntriesSource;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Node;
use kalanis\kw_menu\Interfaces\IEntriesSource;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\Traits\TFilterHtml;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Stuff;
use Traversable;


/**
 * Class Files
 * @package kalanis\kw_menu\EntriesSource
 * Entries source is in passed files adapter
 */
class Files implements IEntriesSource
{
    use TFilterHtml;

    /** @var CompositeAdapter */
    protected $files = null;
    /** @var ArrayPath */
    protected $arrPath = null;

    public function __construct(CompositeAdapter $files)
    {
        $this->files = $files;
        $this->arrPath = new ArrayPath();
    }

    public function getFiles(array $path): Traversable
    {
        try {
            $list = $this->files->readDir($path);
            foreach ($list as $item) {
                /** @var Node $item */
                if (empty($item->getPath())) {
                    // root - skip
                    continue;
                }
                $this->arrPath->setArray($item->getPath());
                if ($this->filterExt(Stuff::fileExt($this->arrPath->getFileName()))) {
                    yield $this->arrPath->getFileName();
                }
            }
        } catch (FilesException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
