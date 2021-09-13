<?php

namespace kalanis\kw_pedigree;


use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_mapper\Search\Search;


/**
 * Class GetEntries
 * @package kalanis\kw_pedigree
 * Getting entries from DB
 */
class GetEntries
{
    /** @var ARecord|null */
    protected $record = null;
    /** @var Storage\AEntryAdapter|null */
    protected $storage = null;

    /**
     * @param ARecord $record
     * @throws PedigreeException
     */
    public function __construct(ARecord $record)
    {
        $this->record = $record;
        $this->storage = Storage\FactoryAdapter::getAdapter($record);
    }

    public function getRecord(): ARecord
    {
        return $this->record;
    }

    public function getStorage(): Storage\AEntryAdapter
    {
        return $this->storage;
    }

    public function getById(string $id): ARecord
    {
        $record = clone $this->record;
        $record->offsetSet($this->storage->getIdKey(), $id);
        $record->load();
        return $record;
    }

    public function getBySex(string $sex): array
    {
        $search = new Search($this->record);
        $search->exact($this->storage->getSexKey(), $sex);
        return $search->getResults();
    }
}