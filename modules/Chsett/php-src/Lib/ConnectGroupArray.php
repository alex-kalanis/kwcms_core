<?php

namespace KWCMS\modules\Chsett\Lib;


use kalanis\kw_connect\core\Connectors\Arrays;
use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class ConnectGroupArray
 * @package KWCMS\modules\Chsett\Lib
 * Mapper is array of connecting items.
 */
class ConnectGroupArray extends Arrays
{
    public function getTranslated($data): IRow
    {
        return new ConnectGroupItem($data);
    }
}
