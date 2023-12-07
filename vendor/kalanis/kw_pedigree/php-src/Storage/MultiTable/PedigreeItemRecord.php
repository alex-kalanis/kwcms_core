<?php

namespace kalanis\kw_pedigree\Storage\MultiTable;


use kalanis\kw_mapper\Interfaces\IEntryType;
use kalanis\kw_pedigree\Interfaces\ISex;
use kalanis\kw_pedigree\Storage\APedigreeRecord;


/**
 * Class PedigreeItemRecord
 * @property PedigreeRelateRecord[] $parents
 * @property PedigreeRelateRecord[] $children
 */
class PedigreeItemRecord extends APedigreeRecord
{
    protected function addEntries(): void
    {
        $this->addEntry('id', IEntryType::TYPE_INTEGER, 2048);
        $this->addEntry('short', IEntryType::TYPE_STRING, 50);
        $this->addEntry('name', IEntryType::TYPE_STRING, 75);
        $this->addEntry('family', IEntryType::TYPE_STRING, 255);
        $this->addEntry('birth', IEntryType::TYPE_STRING, 32);
        $this->addEntry('death', IEntryType::TYPE_STRING, 32);
        $this->addEntry('successes', IEntryType::TYPE_STRING, 255);
        $this->addEntry('sex', IEntryType::TYPE_SET, [ISex::FEMALE, ISex::MALE]);
        $this->addEntry('text', IEntryType::TYPE_STRING, 8192);
        $this->addEntry('parents', IEntryType::TYPE_ARRAY); // FK - makes the array of entries every time
        $this->addEntry('children', IEntryType::TYPE_ARRAY); // FK - makes the array of entries every time
        $this->setMapper($this->getMapperClass());
    }

    /**
     * @return string
     * @codeCoverageIgnore used another one for testing
     */
    protected function getMapperClass(): string
    {
        return PedigreeItemMapper::class;
    }
}
