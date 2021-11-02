<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_table\Connector\Sources\Arrays;
//use kalanis\kw_table\Connector\Sources\Mapper;
use kalanis\kw_table\Interfaces\Table\IRow;


/**
 * Class SourceItem
 * @package KWCMS\modules\Images\Lib
 * Mapper is array of connecting items.
 */
//class SourceItem extends Mapper
class SourceItem extends Arrays
{
    public function getTranslated($data): IRow
    {
        return new ConnectItem($data);
    }
}
