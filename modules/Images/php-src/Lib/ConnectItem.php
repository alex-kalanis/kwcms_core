<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_images\Files;
use kalanis\kw_table\Interfaces\Table\IRow;
use kalanis\kw_tree\FileNode;


/**
 * Class ConnectItem
 * @package KWCMS\modules\Images\Lib
 * Connect single image to table
 */
class ConnectItem implements IRow
{
    protected $array;

    public function __construct(FileNode $item, string $whereDir, Files $libGallery)
    {
        $this->array = [
            'name' => $item->getName(),
            'dir' => $item->getDir(),
            'size' => $item->getSize(),
            'desc' => $libGallery->getLibDesc()->get($whereDir . DIRECTORY_SEPARATOR . $item->getPath()),
            'thumb' => $libGallery->getLibThumb()->getPath($whereDir . DIRECTORY_SEPARATOR . $item->getPath()),
        ];
    }

    public function getValue($property)
    {
        return $this->array[$property];
    }

    public function __isset($name)
    {
        return isset($this->array[$name]);
    }
}
