<?php

namespace KWCMS\modules\Chsett\Lib;


use kalanis\kw_connect\core\Connectors\Arrays;
use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class ConnectUserArray
 * @package KWCMS\modules\Chsett\Lib
 * Mapper is array of connecting items.
 */
class ConnectUserArray extends Arrays
{
    public function getTranslated($data): IRow
    {
        return new ConnectUserItem($data);
    }
}
