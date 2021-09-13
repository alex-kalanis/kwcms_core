<?php

namespace kalanis\kw_pedigree\Storage;


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
        return $this->record->id;
    }

    public function setName(string $name): IEntry
    {
        $this->record->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->record->name;
    }

    public function setFamily(string $family): IEntry
    {
        $this->record->kennel = $family;
        return $this;
    }

    public function getFamily(): string
    {
        return $this->record->kennel;
    }

    public function setBirth(string $birth): IEntry
    {
        $this->record->birth = $birth;
        return $this;
    }

    public function getBirth(): string
    {
        return $this->record->birth;
    }

    public function setFatherId(string $fatherId): IEntry
    {
        $this->record->fatherId = $fatherId;
        return $this;
    }

    public function getFatherId(): string
    {
        return $this->record->fatherId;
    }

    public function setMotherId(string $motherId): IEntry
    {
        $this->record->motherId = $motherId;
        return $this;
    }

    public function getMotherId(): string
    {
        return $this->record->motherId;
    }

    public function getChildren(): array
    {
        $search = new Search($this->record);
        $search->exact('motherId', $this->record->id);
        $search->exact('fatherId', $this->record->id);
        $search->useOr();
        return $search->getResults();
    }

    public function setTrials(string $trials): IEntry
    {
        $this->record->trials = $trials;
        return $this;
    }

    public function getTrials(): string
    {
        return $this->record->trials;
    }

    public function setBreed(string $breed): IEntry
    {
        $this->record->breed = $breed;
        return $this;
    }

    public function getBreed(): string
    {
        return $this->record->breed;
    }

    public function setSex(string $sex): IEntry
    {
        $this->record->sex = $sex;
        return $this;
    }

    public function getSex(): string
    {
        return $this->record->sex;
    }

    public function setText(string $text): IEntry
    {
        $this->record->text = $text;
        return $this;
    }

    public function getText(): string
    {
        return $this->record->text;
    }
}
