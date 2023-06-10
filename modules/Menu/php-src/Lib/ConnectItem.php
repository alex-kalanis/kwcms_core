<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_connect\core\Interfaces\IRow;
use kalanis\kw_menu\Menu\Entry;


class ConnectItem implements IRow
{
    /** @var array<string, string|int> */
    protected $array;

    public function __construct(Entry $item)
    {
        $this->array = [
            'id' => $item->getId(),
            'name' => $item->getName(),
            'desc' => $item->getDesc(),
            'pos' => $item->getPosition(),
            'sub' => intval($item->canGoSub()),
        ];
    }

    public function getValue($property)
    {
        return $this->__isset($property) ? $this->array[$property] : null ;
    }

    public function __isset($property)
    {
        return isset($this->array[$property]);
    }
}
