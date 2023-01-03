<?php

namespace KWCMS\modules\Short\Lib;


use kalanis\kw_mapper\Storage\Shared\FormatFiles;


/**
 * Class SeparatedElements
 * @package KWCMS\modules\Short\Lib
 * Formats/unpack content into/from table created by separated elements in file
 */
class SeparatedElements extends FormatFiles\SeparatedElements
{
    protected $delimitLines = "\r\n";
}
