<?php

namespace KWCMS\modules\Short\Lib;


use kalanis\kw_mapper\Storage\File\Formats;


/**
 * Class SeparatedElements
 * @package KWCMS\modules\Short\Lib
 * Formats/unpack content into/from table created by separated elements in file
 */
class SeparatedElements extends Formats\SeparatedElements
{
    protected $delimitLines = "\r\n";
}
