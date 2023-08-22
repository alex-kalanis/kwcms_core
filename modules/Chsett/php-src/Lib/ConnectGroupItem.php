<?php

namespace KWCMS\modules\Chsett\Lib;


use kalanis\kw_auth_sources\Interfaces\IGroup;
use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class ConnectGroupItem
 * @package KWCMS\modules\Chsett\Lib
 * Connect single image to table
 */
class ConnectGroupItem implements IRow
{
    protected $array;

    public function __construct(IGroup $group)
    {
        $this->array = [
            'id' => $group->getGroupId(),
            'name' => $group->getGroupName(),
            'desc' => $group->getGroupDesc(),
            'authorId' => $group->getGroupAuthorId(),
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
