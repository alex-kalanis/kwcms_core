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
    public function setFatherId(string $fatherId): bool
    {
        return $this->setParentId($fatherId, IEntry::SEX_MALE);
    }

    public function setMotherId(string $motherId): bool
    {
        return $this->setParentId($motherId, IEntry::SEX_FEMALE);
    }

    protected function setParentId(string $parentId, string $sex): bool
    {
        $result = $this->parentLookup($sex);
        if (!empty($result) && !empty($parentId)) {
            // rewrite record
            if ($result->parentId != $parentId) {
                $result->parentId = $parentId;
                return $result->save();
            }
            return true;
        } elseif (!empty($parentId)) {
            // new one
            /** @var PedigreeRelateRecord $record */
            $record = new PedigreeRelateRecord();
            $record->childId = $this->getId();
            $record->parentId = $parentId;
            return $record->save();
        } else {
            // remove current one
            return $result->delete();
        }
    }

    public function getFatherId(): string
    {
        return $this->getParentId(IEntry::SEX_MALE);
    }

    public function getMotherId(): string
    {
        return $this->getParentId(IEntry::SEX_FEMALE);
    }

    protected function getParentId(string $sex): string
    {
        $results = $this->parentLookup($sex);
        return empty($results) ? '' : strval($results->parentId);
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
        return strval($record->childId);
    }

    public function saveFamily(string $fatherId, string $motherId): bool
    {
        return $this->setFatherId($fatherId) && $this->setMotherId($motherId);
    }
}
