<?php

namespace kalanis\kw_pedigree\Storage\File;


use kalanis\kw_mapper\Search\Search;
use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage\AEntryAdapter;
use kalanis\kw_pedigree\Storage\APedigreeRecord;


/**
 * Class EntryAdapter
 * @package kalanis\kw_pedigree\Storage\File
 */
class EntryAdapter extends AEntryAdapter
{
    public function getChildren(): array
    {
        $search1 = new Search($this->getClearRecord());
        $search1->exact('motherId', strval($this->getLoadedRecord()->id));

        $search2 = new Search($this->getClearRecord());
        $search2->exact('fatherId', strval($this->getLoadedRecord()->id));

        // Files cannot use OR in their searches
        return $search1->getResults() + $search2->getResults();
    }

    public function setFatherId(?int $fatherId): ?bool
    {
        if ($this->getLoadedRecord()->fatherId != $fatherId) {
            $this->getLoadedRecord()->fatherId = strval($fatherId);
            return true;
        }
        return null;
    }

    public function getFatherId(): ?int
    {
        $data = $this->getLoadedRecord()->fatherId;
        return !empty($data) ? intval($data) : null;
    }

    public function setMotherId(?int $motherId): ?bool
    {
        if ($this->getLoadedRecord()->motherId != $motherId) {
            $this->getLoadedRecord()->motherId = strval($motherId);
            return true;
        }
        return null;
    }

    public function getMotherId(): ?int
    {
        $data = $this->getLoadedRecord()->motherId;
        return !empty($data) ? intval($data) : null;
    }

    /**
     * @throws PedigreeException
     * @return PedigreeRecord
     */
    public function getLoadedRecord(): APedigreeRecord
    {
        return parent::getLoadedRecord();
    }

    /**
     * Unhook the original class, use only its definition and create new clear copy
     * @throws PedigreeException
     * @return APedigreeRecord
     */
    protected function getClearRecord(): APedigreeRecord
    {
        $record = get_class($this->getLoadedRecord());
        return new $record();
    }
}
