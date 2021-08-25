<?php

namespace kalanis\kw_tree;


use CallbackFilterIterator;
use FilesystemIterator;
use kalanis\kw_paths\Path;
use kalanis\kw_tree\Interfaces\ITree;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;


/**
 * class Tree
 * @package kalanis\kw_tree
 * Main class for work with trees
 */
class Tree
{
    protected $nodeAdapter = null;
    protected $loadRecursive = false;
    protected $rootDir = '';
    protected $startFromPath = '';
    protected $filterCallback = null;
    protected $loadedTree = null;

    public function __construct(Path $path)
    {
        $this->rootDir = realpath($path->getDocumentRoot() . $path->getPathToSystemRoot()) . DIRECTORY_SEPARATOR;
        $this->nodeAdapter = new Adapters\NodeAdapter();
    }

    public function canRecursive(bool $recursive): void
    {
        $this->loadRecursive = $recursive;
    }

    public function startFromPath(string $path): void
    {
        if (false !== realpath($this->rootDir . $path)) {
            $this->startFromPath = $path;
            $this->nodeAdapter->cutDir($this->rootDir . $path);
        }
    }

    /**
     * @param callback|callable|null $callback
     */
    public function setFilterCallback($callback = null): void
    {
        $this->filterCallback = $callback;
    }

    public function process(): void
    {
        $flags = FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO ;
        $iter = $this->loadRecursive
            ? new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->rootDir . $this->startFromPath, $flags))
            : new FilesystemIterator($this->rootDir . $this->startFromPath, $flags)
        ;
        $iter = new CallbackFilterIterator($iter, [$this, 'filterDoubleDot']);
        if ($this->filterCallback) {
            $iter = new CallbackFilterIterator($iter, $this->filterCallback);
        }

        /** @var FileNode[] $nodes */
        $nodes = [];
        foreach ($iter as $item) {
            $node = $this->nodeAdapter->process($item);
            $nodes[$node->getPath()] = $node; // full path
        }
        if (empty($nodes[$this->rootDir . $this->startFromPath])) {
            $item = new SplFileInfo($this->rootDir . $this->startFromPath);
            $node = $this->nodeAdapter->process($item);
            $nodes[$node->getPath()] = $node; // root node
            $primary = $node->getPath();
        } else {
            $primary = DIRECTORY_SEPARATOR;
        }
        foreach ($nodes as &$node) {
            if ($nodes[$node->getDir()] !== $node) { // beware of unintended recursion
                $nodes[$node->getDir()]->addSubNode($node); // and now only to parent dir
            }
        }
        $this->loadedTree = $nodes[$primary];
    }

    public function filterDoubleDot(SplFileInfo $info): bool
    {
        return ( ITree::PARENT_DIR != $info->getFilename() ) ;
    }

    public function getTree(): ?FileNode
    {
        return $this->loadedTree;
    }
}
