<?php

namespace KWCMS\modules\Chsett\Lib;


use kalanis\kw_connect\arrays\Connector;
use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class ConnectUserArray
 * @package KWCMS\modules\Chsett\Lib
 * Mapper is array of connecting items.
 */
class ConnectUserArray extends Connector
{
    public function getTranslated($data): IRow
    {
        return new ConnectUserItem($data);
    }
}
