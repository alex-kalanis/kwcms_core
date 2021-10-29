<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_table\Interfaces\Table\IRow;
use kalanis\kw_tree\FileNode;


class ConnectItem implements IRow
{
    protected $array;

    public function __construct(FileNode $item)
    {
        $this->array = [
            'name' => $item->getName(),
            'dir' => $item->getDir(),
            'size' => $item->getSize(),
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
