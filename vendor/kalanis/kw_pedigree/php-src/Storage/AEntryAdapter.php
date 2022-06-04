<?php

namespace kalanis\kw_pedigree\Storage;


use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_pedigree\Interfaces\IEntry;


/**
 * Class AEntryAdapter
 * @package kalanis\kw_pedigree\Storage\File
 */
abstract class AEntryAdapter implements IEntry
{
    use TBasicKeys;

    /** @var File\PedigreeRecord|SingleTable\PedigreeRecord|MultiTable\PedigreeItemRecord|null */
    protected $record = null;

    public function setRecord(ARecord $record): void
    {
        $this->record = $record;
    }

    public function getRecord(): ?ARecord
    {
        return $this->record;
    }

    public function setId(string $id): IEntry
    {
        $this->record->id = $id;
        return $this;
    }

    public function getId(): string
    {
        return strval($this->record->id);
    }

    public function setKey(string $key): IEntry
    {
        $this->record->key = $key;
        return $this;
    }

    public function getKey(): string
    {
        return strval($this->record->key);
    }

    public function setName(string $name): IEntry
    {
        $this->record->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return strval($this->record->name);
    }

    public function setFamily(string $family): IEntry
    {
        $this->record->kennel = $family;
        return $this;
    }

    public function getFamily(): string
    {
        return strval($this->record->kennel);
    }

    public function setBirth(string $birth): IEntry
    {
        $this->record->birth = $birth;
        return $this;
    }

    public function getBirth(): string
    {
        return strval($this->record->birth);
    }

    public function setFatherId(string $fatherId): ?bool
    {
        if ($this->record->fatherId != $fatherId) {
            $this->record->fatherId = $fatherId;
            return true;
        }
        return null;
    }

    public function getFatherId(): string
    {
        return strval($this->record->fatherId);
    }

    public function setMotherId(string $motherId): ?bool
    {
        if ($this->record->motherId != $motherId) {
            $this->record->motherId = $motherId;
            return true;
        }
        return null;
    }

    public function getMotherId(): string
    {
        return strval($this->record->motherId);
    }

    /**
     * @return array
     * @throws MapperException
     */
    public function getChildren(): array
    {
        $search = new Search($this->record);
        $search->exact('motherId', $this->record->id);
        $search->exact('fatherId', $this->record->id);
        $search->useOr();
        return $search->getResults();
    }

    /**
     * @param string $fatherId
     * @param string $motherId
     * @return bool|null
     * @throws MapperException
     */
    public function saveFamily(string $fatherId, string $motherId): ?bool
    {
        if ((bool)$this->setFatherId($fatherId) || (bool)$this->setMotherId($motherId)) {
            return $this->record->save();
        }
        return null;
    }

    public function setTrials(string $trials): IEntry
    {
        $this->record->trials = $trials;
        return $this;
    }

    public function getTrials(): string
    {
        return strval($this->record->trials);
    }

    public function setBreed(string $breed): IEntry
    {
        $this->record->breed = $breed;
        return $this;
    }

    public function getBreed(): string
    {
        return strval($this->record->breed);
    }

    public function setSex(string $sex): IEntry
    {
        $this->record->sex = $sex;
        return $this;
    }

    public function getSex(): string
    {
        return strval($this->record->sex);
    }

    public function setText(string $text): IEntry
    {
        $this->record->text = $text;
        return $this;
    }

    public function getText(): string
    {
        return strval($this->record->text);
    }

    public function getLike(string $what, $sex): array
    {
        return $this->record->getMapper()->getLike($what, empty($sex) ? null : strval($sex));
    }
}
