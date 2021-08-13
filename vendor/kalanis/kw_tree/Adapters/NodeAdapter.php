<?php

namespace kalanis\kw_tree\Adapters;


use kalanis\kw_paths\Stuff;
use kalanis\kw_tree\FileNode;
use kalanis\kw_tree\Interfaces\ITree;
use SplFileInfo;


/**
 * Class NodeAdapter
 * @package kalanis\kw_tree\Adapters
 * Create tree node from SplFileInfo
 */
class NodeAdapter
{
    protected $cutDir = '';

    public function cutDir(string $dir): self
    {
        $this->cutDir = $dir;
        return $this;
    }

    public function process(SplFileInfo $info): FileNode
    {
        $node = new FileNode();
        $path = $this->cutPath($info->getRealPath());
        if (ITree::CURRENT_DIR == $info->getFilename()) {
            $name = Stuff::filename($path);
            $node->setData(
                empty($name) ? DIRECTORY_SEPARATOR : $name,
                Stuff::directory($path),
                empty($name) ? $path : $path . DIRECTORY_SEPARATOR,
                $info->getSize(),
                $info->getType(),
                $info->isReadable(),
                $info->isWritable()
            );
        } else {
            $node->setData(
                $info->getFilename(),
                Stuff::directory($path),
                $path,
                $info->getSize(),
                $info->getType(),
                $info->isReadable(),
                $info->isWritable()
            );
        }
        return $node;
    }

    protected function cutPath(string $path): string
    {
        return DIRECTORY_SEPARATOR . mb_substr($path, mb_strlen($this->cutDir));
    }
}
