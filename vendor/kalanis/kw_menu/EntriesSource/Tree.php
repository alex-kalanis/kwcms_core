<?php

namespace kalanis\kw_menu\EntriesSource;


use kalanis\kw_menu\Interfaces\IEntriesSource;
use kalanis\kw_menu\Traits\TFilterHtml;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Stuff;
use kalanis\kw_tree\Interfaces\ITree;
use Traversable;


/**
 * Class Tree
 * @package kalanis\kw_menu\EntriesSource
 * Entries source is in passed tree
 */
class Tree implements IEntriesSource
{
    use TFilterHtml;

    /** @var ITree */
    protected $tree = null;
    /** @var ArrayPath */
    protected $arrPath = null;

    public function __construct(ITree $tree)
    {
        $this->tree = $tree;
        $this->arrPath = new ArrayPath();
    }

    public function getFiles(array $path): Traversable
    {
        $this->tree->setStartPath($path);
        $this->tree->wantDeep(false);
        $this->tree->process();
        if ($root = $this->tree->getRoot()) {
            foreach ($root->getSubNodes() as $item) {
                $this->arrPath->setArray($item->getPath());
                if ($this->filterExt(Stuff::fileExt($this->arrPath->getFileName()))) {
                    yield $this->arrPath->getFileName();
                }
            }
        }
    }
}
