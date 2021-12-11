<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_connect\core\Interfaces\IRow;
use kalanis\kw_menu\Menu\Item;


class ConnectItem implements IRow
{
    protected $array;

    public function __construct(Item $item)
    {
        $this->array = [
            'file' => $item->getFile(),
            'name' => $item->getName(),
            'desc' => $item->getTitle(),
            'pos' => $item->getPosition(),
            'sub' => intval($item->canGoSub()),
        ];
    }

    public function getValue($property)
    {
        return $this->array[$property];
    }

    public function __isset($property)
    {
        return isset($this->array[$property]);
    }
}
