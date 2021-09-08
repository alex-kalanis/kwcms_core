<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_table\Connector\Sources\Mapper;


/**
 * Class SourceItem
 * @package KWCMS\modules\Menu\Lib
 * Mapper is array of connecting items.
 */
class SourceItem extends Mapper
{
    protected function parseData(): void
    {
        foreach ($this->rawData as $record) {
            $this->translatedData[] = new ConnectItem($record);
        }
    }
}
