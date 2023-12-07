<?php

namespace kalanis\kw_pedigree\Storage\MultiTable;


use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Mappers\Shared\ForeignKey;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_pedigree\Interfaces\ISex;
use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage\AEntryAdapter;


/**
 * Class EntryAdapter
 * @package kalanis\kw_pedigree\Storage\MultiTable
 */
class EntryAdapter extends AEntryAdapter
{
    /**
     * @param int|null $fatherId
     * @throws MapperException
     * @throws PedigreeException
     * @return bool|null
     */
    public function setFatherId(?int $fatherId): ?bool
    {
        return $this->setParentId($fatherId, ISex::MALE);
    }

    /**
     * @param int|null $motherId
     * @throws MapperException
     * @throws PedigreeException
     * @return bool|null
     */
    public function setMotherId(?int $motherId): ?bool
    {
        return $this->setParentId($motherId, ISex::FEMALE);
    }

    /**
     * @param int|null $parentId
     * @param string $sex
     * @throws MapperException
     * @throws PedigreeException
     * @return bool|null
     */
    protected function setParentId(?int $parentId, string $sex): ?bool
    {
        $result = $this->parentLookup($sex);
        if (!empty($result) && !empty($parentId)) {
            // rewrite record
            if ($result->parentId != $parentId) {
                $result->parentId = intval($parentId);
                return $result->save();
            }
            return null;
        } elseif (!empty($parentId)) {
            // new one
            $record = $this->getRelateRecord();
            $record->childId = intval($this->getId());
            $record->parentId = intval($parentId);
            return $record->save();
        } elseif (!empty($result) && empty($parentId)) {
            // remove current one
            return $result->delete();
        } else {
            return null;
        }
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     * @return int|null
     */
    public function getFatherId(): ?int
    {
        return $this->getParentId(ISex::MALE);
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     * @return int|null
     */
    public function getMotherId(): ?int
    {
        return $this->getParentId(ISex::FEMALE);
    }

    /**
     * @param string $sex
     * @throws MapperException
     * @throws PedigreeException
     * @return int|null
     */
    protected function getParentId(string $sex): ?int
    {
        $results = $this->parentLookup($sex);
        return empty($results) ? null : intval($results->parentId);
    }

    /**
     * @param string $sex
     * @throws MapperException
     * @throws PedigreeException
     * @return PedigreeRelateRecord|null
     */
    protected function parentLookup(string $sex): ?PedigreeRelateRecord
    {
        $search = new Search($this->getRelateRecord());
        $search->exact('childId', strval($this->getId()));
        $search->child('parents', '', '', 'par');
        $search->like('par.sex', $sex);
        $results = $search->getResults();
        /** @var PedigreeRelateRecord[] $results */
        return empty($results) ? null : reset($results);
    }

    public function getChildren(): array
    {
        $searchRelation = new Search($this->getRelateRecord());
        $searchRelation->exact('parentId', strval($this->getId()));
        $search = new Search($this->getLoadedRecord());
        $search->in('id', array_map([$this, 'mapChildren'], $searchRelation->getResults() ));
        return $search->getResults();
    }

    public function mapChildren(PedigreeRelateRecord $record): string
    {
        return strval($record->childId);
    }

    public function saveFamily(?int $fatherId, ?int $motherId): ?bool
    {
        $willSave = null;
        $action = $this->setFatherId($fatherId);
        if (is_bool($action)) {
            $willSave = $action;
        }
        $action = $this->setMotherId($motherId);
        if (is_bool($action)) {
            $willSave = (is_bool($willSave) ? $willSave : true) && $action;
        }
        return $willSave;
    }

    /**
     * Unhook the original class, use only its definition and create new clear copy
     * @throws MapperException
     * @throws PedigreeException
     * @return PedigreeRelateRecord
     */
    protected function getRelateRecord(): PedigreeRelateRecord
    {
        $fks = $this->getLoadedRecord()->getMapper()->getForeignKeys();
        /** @var array<string, ForeignKey> $fks */
        if (!isset($fks['children'])) {
            // @codeCoverageIgnoreStart
            throw new PedigreeException('Your mapper does not have children');
        }
        // @codeCoverageIgnoreEnd
        $record = $fks['children']->getRemoteRecord();
        return new $record();
    }
}
