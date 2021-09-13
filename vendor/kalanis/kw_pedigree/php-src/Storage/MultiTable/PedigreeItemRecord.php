<?php

namespace kalanis\kw_pedigree\Storage\MultiTable;


use kalanis\kw_mapper\Interfaces\IEntryType;
use kalanis\kw_mapper\Records\ASimpleRecord;


/**
 * Class PedigreeItemRecord
 * @property int id
 * @property string key
 * @property string name
 * @property string kennel
 * @property string birth
 * @property string address
 * @property string trials
 * @property string photo
 * @property int photoX
 * @property int photoY
 * @property string breed
 * @property string sex
 * @property string text
 * @property PedigreeRelateRecord[] parents
 * @property PedigreeRelateRecord[] children
 */
class PedigreeItemRecord extends ASimpleRecord
{
    protected function addEntries(): void
    {
        $this->addEntry('id', IEntryType::TYPE_INTEGER, 2048);
        $this->addEntry('key', IEntryType::TYPE_STRING, 50);
        $this->addEntry('name', IEntryType::TYPE_STRING, 75);
        $this->addEntry('kennel', IEntryType::TYPE_STRING, 255);
        $this->addEntry('birth', IEntryType::TYPE_STRING, 32);
        $this->addEntry('address', IEntryType::TYPE_STRING, 255);
        $this->addEntry('trials', IEntryType::TYPE_STRING, 255);
        $this->addEntry('photo', IEntryType::TYPE_STRING, 255);
        $this->addEntry('photoX', IEntryType::TYPE_INTEGER, 2048);
        $this->addEntry('photoY', IEntryType::TYPE_INTEGER, 2048);
        $this->addEntry('breed', IEntryType::TYPE_SET, ['no','yes','died']);
        $this->addEntry('sex', IEntryType::TYPE_SET, ['female','male']);
        $this->addEntry('blood', IEntryType::TYPE_SET, ['our','other']);
        $this->addEntry('text', IEntryType::TYPE_STRING, 8192);
        $this->addEntry('parents', IEntryType::TYPE_ARRAY); // FK - makes the array of entries every time
        $this->addEntry('children', IEntryType::TYPE_ARRAY); // FK - makes the array of entries every time
        $this->setMapper('\kalanis\kw_pedigree\Storage\MultiTable\PedigreeItemMapper');
    }
}
