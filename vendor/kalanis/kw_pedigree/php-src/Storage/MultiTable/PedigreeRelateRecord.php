<?php

namespace kalanis\kw_pedigree\Storage\MultiTable;


use kalanis\kw_mapper\Interfaces\IEntryType;
use kalanis\kw_mapper\Records\ASimpleRecord;


/**
 * Class PedigreeRelateRecord
 * @property int $id
 * @property int $childId
 * @property int $parentId
 * @property PedigreeItemRecord[] $parents
 * @property PedigreeItemRecord[] $children
 */
class PedigreeRelateRecord extends ASimpleRecord
{
    protected function addEntries(): void
    {
        $this->addEntry('id', IEntryType::TYPE_INTEGER, 2048);
        $this->addEntry('childId', IEntryType::TYPE_INTEGER, 2048);
        $this->addEntry('parentId', IEntryType::TYPE_INTEGER, 2048);
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
        return PedigreeRelateMapper::class;
    }
}
