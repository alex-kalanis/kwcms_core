<?php

namespace kalanis\kw_tree;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessDirs;
use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
use kalanis\kw_paths\Path;
use kalanis\kw_paths\Stuff;


/**
 * class Tree
 * @package kalanis\kw_tree
 * Main class for work with trees
 */
class Tree
{
    /** @var Adapters\NodeAdapter */
    protected $nodeAdapter = null;
    /** @var bool */
    protected $loadRecursive = false;
    /** @var string */
    protected $rootDir = '';
    /** @var string */
    protected $startFromPath = '';
    /** @var callback|callable|null */
    protected $filterCallback = null;
    /** @var FileNode|null */
    protected $loadedTree = null;
    /** @var IProcessDirs */
    protected $processor = null;

    public function __construct(Path $path, IProcessDirs $processor)
    {
        $this->rootDir = realpath($path->getDocumentRoot() . $path->getPathToSystemRoot()) . DIRECTORY_SEPARATOR;
        $this->nodeAdapter = new Adapters\NodeAdapter();
        $this->processor = $processor;
    }

    public function canRecursive(bool $recursive): void
    {
        $this->loadRecursive = $recursive;
    }

    public function startFromPath(string $path): void
    {
        if (false !== ($knownPath = realpath($this->rootDir . $path))) {
            $this->startFromPath = $path;
            $this->nodeAdapter->cutDir($knownPath . DIRECTORY_SEPARATOR);
        }
    }

    /**
     * @param callback|callable|null $callback
     */
    public function setFilterCallback($callback = null): void
    {
        $this->filterCallback = $callback;
    }

    /**
     * @throws FilesException
     */
    public function process(): void
    {
        $targetPath = [$this->rootDir, $this->startFromPath];
        /** @var Node[] $iter */
        $iter = $this->processor->readDir($targetPath, $this->loadRecursive);
        if ($this->filterCallback) {
            $iter = array_filter($iter, $this->filterCallback);
        }

        /** @var FileNode[] $nodes */
        $nodes = [];
        foreach ($iter as $item) {
            $eachNode = $this->nodeAdapter->process($item);
            $nodes[$this->getKey($eachNode)] = $eachNode; // full path
        }
        if (isset($nodes[DIRECTORY_SEPARATOR])) {
            $nodes[''] = $nodes[DIRECTORY_SEPARATOR];
            unset($nodes[DIRECTORY_SEPARATOR]);
        }
        if (empty($nodes[''])) { // root dir has no upper path
            $item = new Node();
            $item->setData([$this->rootDir, $this->startFromPath], 0, ITypes::TYPE_DIR);
            $rootNode = $this->nodeAdapter->process($item);
            $nodes[''] = $rootNode; // root node
        }

//print_r($nodes);
        foreach ($nodes as $index => &$node) {
            if ('' != $index) { // not parent for root
                if ($nodes[$node->getDir()] !== $node) { // beware of unintended recursion
                    $nodes[$node->getDir()]->addSubNode($node); // and now only to parent dir
                }
            }
        }
        $this->loadedTree = $nodes[''];
//print_r($this->loadedTree);
    }

    protected function getKey(FileNode $node): string
    {
        return $node->isDir()
            ? (empty($node->getPath())
                ? $node->getName()
                : Stuff::removeEndingSlash($node->getPath()) . DIRECTORY_SEPARATOR
            )
            : $node->getPath()
        ;
    }

    public function getTree(): ?FileNode
    {
        return $this->loadedTree;
    }
}
