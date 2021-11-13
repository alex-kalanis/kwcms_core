<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_connect\Interfaces\IRow;
use kalanis\kw_connect_record\Connector;


/**
 * Class ItemConnector
 * @package KWCMS\modules\Menu\Lib
 * Mapper is array of connecting items.
 */
class ItemConnector extends Connector
{
    public function getTranslated($data): IRow
    {
        return new ConnectItem($data);
    }
}
