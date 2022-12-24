<?php

namespace KWCMS\modules\Short\Lib;


use kalanis\kw_mapper\Interfaces\IEntryType;
use kalanis\kw_mapper\Records\ASimpleRecord;


/**
 * Class ShortMessage
 * @package KWCMS\modules\Short\Lib
 * @property int id
 * @property int date
 * @property string title
 * @property string content
 */
class ShortMessage extends ASimpleRecord
{
    protected function addEntries(): void
    {
        $this->addEntry('id', IEntryType::TYPE_INTEGER, 4096);
        $this->addEntry('date', IEntryType::TYPE_INTEGER, PHP_INT_MAX);
        $this->addEntry('title', IEntryType::TYPE_STRING, 1024);
        $this->addEntry('content', IEntryType::TYPE_STRING, 8192);
        $this->setMapper(ShortMessageMapper::class);
    }
}
