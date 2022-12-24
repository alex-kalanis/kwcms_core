<?php

namespace kalanis\kw_pedigree\Storage\SingleTable;


use kalanis\kw_mapper\Interfaces\IEntryType;
use kalanis\kw_mapper\Records\ASimpleRecord;


/**
 * Class PedigreeRecord
 * @package kalanis\kw_pedigree\Storage\SingleTable
 * @property string id
 * @property string name
 * @property string kennel
 * @property string birth
 * @property string fatherId
 * @property string motherId
 * @property string address
 * @property string trials
 * @property string photo
 * @property string photoX
 * @property string photoY
 * @property string breed
 * @property string sex
 * @property string text
 * @property PedigreeMapper[] father
 * @property PedigreeMapper[] mother
 */
class PedigreeRecord extends ASimpleRecord
{
    protected function addEntries(): void
    {
        $this->addEntry('id', IEntryType::TYPE_STRING, 50);
        $this->addEntry('name', IEntryType::TYPE_STRING, 75);
        $this->addEntry('kennel', IEntryType::TYPE_STRING, 255);
        $this->addEntry('birth', IEntryType::TYPE_STRING, 32);
        $this->addEntry('fatherId', IEntryType::TYPE_STRING, 50);
        $this->addEntry('motherId', IEntryType::TYPE_STRING, 50);
        $this->addEntry('address', IEntryType::TYPE_STRING, 255);
        $this->addEntry('trials', IEntryType::TYPE_STRING, 255);
        $this->addEntry('photo', IEntryType::TYPE_STRING, 255);
        $this->addEntry('photoX', IEntryType::TYPE_INTEGER, 2048);
        $this->addEntry('photoY', IEntryType::TYPE_INTEGER, 2048);
        $this->addEntry('breed', IEntryType::TYPE_SET, ['no','yes','died', '']);
        $this->addEntry('sex', IEntryType::TYPE_SET, ['female','male']);
        $this->addEntry('blood', IEntryType::TYPE_SET, ['our','other', '']);
        $this->addEntry('text', IEntryType::TYPE_STRING, 8192);
        $this->addEntry('father', IEntryType::TYPE_ARRAY); // FK - makes the array of entries every time
        $this->addEntry('mother', IEntryType::TYPE_ARRAY); // FK - makes the array of entries every time
        $this->setMapper(PedigreeMapper::class);
    }
}
