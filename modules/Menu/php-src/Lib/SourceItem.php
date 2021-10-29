<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_table\Connector\Sources\Mapper;
use kalanis\kw_table\Interfaces\Table\IRow;


/**
 * Class SourceItem
 * @package KWCMS\modules\Menu\Lib
 * Mapper is array of connecting items.
 */
class SourceItem extends Mapper
{
    public function getTranslated($data): IRow
    {
        return new ConnectItem($data);
    }
}
