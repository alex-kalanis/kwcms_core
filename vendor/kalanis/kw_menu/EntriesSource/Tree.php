<?php

namespace kalanis\kw_menu\EntriesSource;


use kalanis\kw_files\Processing\Volume\ProcessDir;
use kalanis\kw_menu\Interfaces\IEntriesSource;
use kalanis\kw_paths\Path;
use kalanis\kw_tree\Tree as XTree;
use SplFileInfo;
use Traversable;


/**
 * Class Tree
 * @package kalanis\kw_menu\EntriesSource
 * Entries source is in passed tree
 */
class Tree implements IEntriesSource
{
    use TFilterHtml;

    /** @var XTree */
    protected $tree = null;

    public function __construct(Path $path)
    {
        $this->tree = new XTree($path, new ProcessDir());
    }

    public function getFiles(string $dir): Traversable
    {
        $this->tree->startFromPath($dir);
        $this->tree->canRecursive(false);
        $this->tree->setFilterCallback([$this, 'filterHtml']);
        $this->tree->process();
        foreach ($this->tree->getTree()->getSubNodes() as $item) {
            yield $item->getName();
        }
    }

    public function filterHtml(SplFileInfo $info): bool
    {
        return $this->filterExt($info->getExtension());
    }
}
