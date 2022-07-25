<?php

namespace kalanis\kw_tree;


use kalanis\kw_files\Interfaces\ITypes;


/**
 * Class FileNode
 * @package kalanis\kw_tree
 * File in directory (could be directory too)
 * Different, yet similar to SplFileInfo because it's possible to pack and unpack the whole thing without access to real volume
 */
class FileNode
{
    /** @var string */
    protected $path = '';
    /** @var string */
    protected $dir = '';
    /** @var string */
    protected $name = '';
    /** @var string */
    protected $type = ITypes::TYPE_UNKNOWN;
    /** @var int */
    protected $size = 0;
    /** @var FileNode[] */
    protected $subNodes = [];

    public function setData(string $path, string $dir, string $name, int $size, string $type): self
    {
        $this->path = $path;
        $this->dir = $dir;
        $this->name = $name;
        $this->size = $size;
        $this->type = $type;
        return $this;
    }

    public function addSubNode(FileNode $node): self
    {
        $this->subNodes[] = $node;
        return $this;
    }

    /**
     * @return FileNode[]
     */
    public function getSubNodes(): array
    {
        return $this->subNodes;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDir(): string
    {
        return $this->dir;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isFile(): bool
    {
        return ITypes::TYPE_FILE == $this->type;
    }

    public function isDir(): bool
    {
        return ITypes::TYPE_DIR == $this->type;
    }

    public function isLink(): bool
    {
        return ITypes::TYPE_LINK == $this->type;
    }
}
