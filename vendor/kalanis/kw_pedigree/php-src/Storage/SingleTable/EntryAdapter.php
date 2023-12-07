<?php

namespace kalanis\kw_pedigree\Storage\SingleTable;


use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage\AEntryAdapter;
use kalanis\kw_pedigree\Storage\APedigreeRecord;


/**
 * Class EntryAdapter
 * @package kalanis\kw_pedigree\Storage\SingleTable
 */
class EntryAdapter extends AEntryAdapter
{
    public function setFatherId(?int $fatherId): ?bool
    {
        if ($this->getLoadedRecord()->fatherId != $fatherId) {
            $this->getLoadedRecord()->fatherId = $fatherId;
            return true;
        }
        return null;
    }

    public function getFatherId(): ?int
    {
        $data = $this->getLoadedRecord()->fatherId;
        return !is_null($data) ? intval($data) : null;
    }

    public function setMotherId(?int $motherId): ?bool
    {
        if ($this->getLoadedRecord()->motherId != $motherId) {
            $this->getLoadedRecord()->motherId = $motherId;
            return true;
        }
        return null;
    }

    public function getMotherId(): ?int
    {
        $data = $this->getLoadedRecord()->motherId;
        return !is_null($data) ? intval($data) : null;
    }

    /**
     * @throws PedigreeException
     * @return PedigreeRecord
     */
    public function getLoadedRecord(): APedigreeRecord
    {
        return parent::getLoadedRecord();
    }
}
