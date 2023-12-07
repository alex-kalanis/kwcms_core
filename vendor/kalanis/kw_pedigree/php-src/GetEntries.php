<?php

namespace kalanis\kw_pedigree;


use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_pedigree\Storage\APedigreeRecord;


/**
 * Class GetEntries
 * @package kalanis\kw_pedigree
 * Getting entries from DB
 */
class GetEntries
{
    /** @var APedigreeRecord */
    protected $record = null;
    /** @var Storage\AEntryAdapter */
    protected $storage = null;

    /**
     * @param APedigreeRecord $record
     * @throws PedigreeException
     */
    public function __construct(APedigreeRecord $record)
    {
        $this->record = $record;
        $this->storage = Storage\FactoryAdapter::getAdapter($record);
    }

    public function getRecord(): APedigreeRecord
    {
        return $this->record;
    }

    public function getStorage(): Storage\AEntryAdapter
    {
        return $this->storage;
    }

    /**
     * @param int $id
     * @throws MapperException
     * @return Storage\AEntryAdapter|null
     */
    public function getById(int $id): ?Storage\AEntryAdapter
    {
        return $this->fillStorage($this->getBy($this->storage->getIdKey(), strval($id)));
    }

    /**
     * @param string $key
     * @throws MapperException
     * @return Storage\AEntryAdapter|null
     */
    public function getByKey(string $key): ?Storage\AEntryAdapter
    {
        return $this->fillStorage($this->getBy($this->storage->getShortKey(), $key));
    }

    /**
     * @param string $key
     * @param string $value
     * @throws MapperException
     * @return APedigreeRecord|null
     */
    protected function getBy(string $key, string $value): ?APedigreeRecord
    {
        $search = new Search(clone $this->record);
        $search->exact($key, $value);
        $results = $search->getResults();
        return empty($results) ? null : reset($results);
    }

    /**
     * @param string $sex
     * @param string|null $name which name
     * @param string|null $family from which family will be get
     * @param string|null $emptyString If you want to add empty record, you need to set an empty string; usually for empty choice
     * @throws MapperException
     * @return Storage\AEntryAdapter[]
     */
    public function getBySex(string $sex, ?string $name = null, ?string $family = null, ?string $emptyString = null): array
    {
        $search = new Search(clone $this->record);
        if (!is_null($name)) {
            $search->like($this->storage->getNameKey(), $name);
        }
        if (!is_null($family)) {
            $search->like($this->storage->getFamilyKey(), $family);
        }
        $search->exact($this->storage->getSexKey(), $sex);
        if (is_null($emptyString)) {
            return array_filter(array_map([$this, 'fillStorage'], $search->getResults()));
        }
        $emptyRecord = clone $this->record;
        $emptyRecord->offsetSet($this->storage->getIdKey(), '');
        $emptyRecord->offsetSet($this->storage->getShortKey(), '');
        $emptyRecord->offsetSet($this->storage->getNameKey(), $emptyString);
        $emptyRecord->offsetSet($this->storage->getFamilyKey(), '');
        $emptyRecord->offsetSet($this->storage->getBirthKey(), '');
        $emptyRecord->offsetSet($this->storage->getDeathKey(), '');
        $emptyRecord->offsetSet($this->storage->getSuccessesKey(), '');
        $emptyRecord->offsetSet($this->storage->getSexKey(), 'none');
        $emptyRecord->offsetSet($this->storage->getTextKey(), '');
        return array_filter(array_map([$this, 'fillStorage'], array_merge([$emptyRecord], $search->getResults())));
    }

    public function fillStorage(?APedigreeRecord $record): ?Storage\AEntryAdapter
    {
        if (empty($record)) {
            return null;
        }
        $storage = clone $this->storage;
        $storage->setRecord($record);
        return $storage;
    }
}
