<?php

namespace kalanis\kw_pedigree\Storage\File;


use kalanis\kw_mapper\Interfaces\IEntryType;
use kalanis\kw_pedigree\Interfaces\ISex;
use kalanis\kw_pedigree\Storage\APedigreeRecord;


/**
 * Class PedigreeRecord
 * @package kalanis\kw_pedigree\Storage\File
 * @property string $fatherId
 * @property string $motherId
 */
class PedigreeRecord extends APedigreeRecord
{
    protected function addEntries(): void
    {
        $this->addEntry('id', IEntryType::TYPE_INTEGER, 65536);
        $this->addEntry('short', IEntryType::TYPE_STRING, 64);
        $this->addEntry('name', IEntryType::TYPE_STRING, 75);
        $this->addEntry('family', IEntryType::TYPE_STRING, 255);
        $this->addEntry('birth', IEntryType::TYPE_STRING, 32);
        $this->addEntry('death', IEntryType::TYPE_STRING, 32);
        $this->addEntry('fatherId', IEntryType::TYPE_INTEGER, 65536);
        $this->addEntry('motherId', IEntryType::TYPE_INTEGER, 65536);
        $this->addEntry('successes', IEntryType::TYPE_STRING, 255);
        $this->addEntry('sex', IEntryType::TYPE_SET, [ISex::MALE, ISex::FEMALE]);
        $this->addEntry('text', IEntryType::TYPE_STRING, 8192);
        $this->setMapper(PedigreeMapper::class);
    }
}
