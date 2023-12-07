<?php

namespace kalanis\kw_pedigree\Storage\SingleTable;


use kalanis\kw_mapper\Interfaces\IEntryType;
use kalanis\kw_pedigree\Interfaces\ISex;
use kalanis\kw_pedigree\Storage\APedigreeRecord;


/**
 * Class PedigreeRecord
 * @package kalanis\kw_pedigree\Storage\SingleTable
 * @property int|null $fatherId
 * @property int|null $motherId
 * @property PedigreeMapper[] $father
 * @property PedigreeMapper[] $mother
 */
class PedigreeRecord extends APedigreeRecord
{
    protected function addEntries(): void
    {
        $this->addEntry('id', IEntryType::TYPE_INTEGER, 65536);
        $this->addEntry('short', IEntryType::TYPE_STRING, 64);
        $this->addEntry('name', IEntryType::TYPE_STRING, 75);
        $this->addEntry('family', IEntryType::TYPE_STRING, 256);
        $this->addEntry('birth', IEntryType::TYPE_STRING, 32);
        $this->addEntry('death', IEntryType::TYPE_STRING, 32);
        $this->addEntry('fatherId', IEntryType::TYPE_INTEGER, 65536);
        $this->addEntry('motherId', IEntryType::TYPE_INTEGER, 65536);
        $this->addEntry('successes', IEntryType::TYPE_STRING, 1024);
        $this->addEntry('sex', IEntryType::TYPE_SET, [ISex::FEMALE, ISex::MALE]);
        $this->addEntry('text', IEntryType::TYPE_STRING, 8192);
        $this->addEntry('father', IEntryType::TYPE_ARRAY); // FK - makes the array of entries every time
        $this->addEntry('mother', IEntryType::TYPE_ARRAY); // FK - makes the array of entries every time
        $this->setMapper($this->getMapperClass());
    }

    /**
     * @return string
     * @codeCoverageIgnore used another one for testing
     */
    protected function getMapperClass(): string
    {
        return PedigreeMapper::class;
    }
}
