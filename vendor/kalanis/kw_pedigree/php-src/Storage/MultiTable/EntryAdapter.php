<?php

namespace kalanis\kw_pedigree\Storage\MultiTable;


use kalanis\kw_mapper\Search\Search;
use kalanis\kw_pedigree\Interfaces\IEntry;
use kalanis\kw_pedigree\Storage\AEntryAdapter;


/**
 * Class EntryAdapter
 * @package kalanis\kw_pedigree\Storage\MultiTable
 */
class EntryAdapter extends AEntryAdapter
{
    public function setFatherId(string $fatherId): IEntry
    {
        $result = $this->parentLookup(IEntry::SEX_MALE);
        if (!empty($result)) {
            // rewrite record
            $result->parentId = $fatherId;
            $result->save();
        } else {
            // new one
            /** @var PedigreeRelateRecord $record */
            $record = new PedigreeRelateRecord();
            $record->childId = $this->getId();
            $record->parentId = $fatherId;
            $record->save();
        }
        return $this;
    }

    public function getFatherId(): string
    {
        $results = $this->parentLookup(IEntry::SEX_MALE);
        return empty($results) ? '' : $results->parentId;
    }

    public function setMotherId(string $motherId): IEntry
    {
        $result = $this->parentLookup(IEntry::SEX_FEMALE);
        if (!empty($result)) {
            // rewrite record
            $result->parentId = $motherId;
            $result->save();
        } else {
            // new one
            /** @var PedigreeRelateRecord $record */
            $record = new PedigreeRelateRecord();
            $record->childId = $this->getId();
            $record->parentId = $motherId;
            $record->save();
        }
        return $this;
    }

    public function getMotherId(): string
    {
        $results = $this->parentLookup(IEntry::SEX_FEMALE);
        return empty($results) ? '' : $results->parentId;
    }

    protected function parentLookup(string $sex): ?PedigreeRelateRecord
    {
        $search = new Search(new PedigreeRelateRecord());
        $search->exact('childId', $this->getId());
        $search->child('children', '', '', 'chil');
        $search->like('chil.sex', $sex);
        $results = $search->getResults();
        return empty($results) ? null : reset($results);
    }

    public function getChildren(): array
    {
        $searchRelation = new Search(new PedigreeRelateRecord());
        $searchRelation->exact('parentId', $this->record->id);
        $search = new Search($this->record);
        $search->in('id', array_map([$this, 'mapChildren'], $searchRelation->getResults() ));
        return $search->getResults();
    }

    public function mapChildren(PedigreeRelateRecord $record): string
    {
        return $record->childId;
    }
}
