<?php

namespace KWCMS\modules\Rss\Lib;


use kalanis\kw_mapper\Storage\File\Formats;


/**
 * Class SeparatedElements
 * @package KWCMS\modules\Rss\Lib
 * Formats/unpack content into/from table created by separated elements in file
 */
class SeparatedElements extends Formats\SeparatedElements
{
    protected $delimitLines = "\r\n";
}
