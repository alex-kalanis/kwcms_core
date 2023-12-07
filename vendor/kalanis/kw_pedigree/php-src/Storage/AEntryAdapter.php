<?php

namespace kalanis\kw_pedigree\Storage;


use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_pedigree\Interfaces\IEntry;
use kalanis\kw_pedigree\PedigreeException;


/**
 * Class AEntryAdapter
 * @package kalanis\kw_pedigree\Storage\File
 */
abstract class AEntryAdapter implements IEntry
{
    use TBasicKeys;

    /** @var APedigreeRecord|null */
    protected $record = null;

    public function setRecord(?APedigreeRecord $record): void
    {
        $this->record = $record;
    }

    public function getRecord(): ?APedigreeRecord
    {
        return $this->record;
    }

    /**
     * @throws PedigreeException
     * @return APedigreeRecord
     */
    public function getLoadedRecord(): APedigreeRecord
    {
        if (empty($this->record)) {
            throw new PedigreeException('No basic record set!');
        }
        return $this->record;
    }

    public function setId(int $id): IEntry
    {
        $this->getLoadedRecord()->id = $id;
        return $this;
    }

    public function getId(): int
    {
        return intval($this->getLoadedRecord()->id);
    }

    public function setShort(string $key): IEntry
    {
        $this->getLoadedRecord()->short = $key;
        return $this;
    }

    public function getShort(): string
    {
        return strval($this->getLoadedRecord()->short);
    }

    public function setName(string $name): IEntry
    {
        $this->getLoadedRecord()->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return strval($this->getLoadedRecord()->name);
    }

    public function setFamily(string $family): IEntry
    {
        $this->getLoadedRecord()->family = $family;
        return $this;
    }

    public function getFamily(): string
    {
        return strval($this->getLoadedRecord()->family);
    }

    public function setBirth(?string $birth): IEntry
    {
        $this->getLoadedRecord()->birth = strval($birth);
        return $this;
    }

    public function getBirth(): ?string
    {
        $data = $this->getLoadedRecord()->birth;
        return empty($data) ? null : strval($data);
    }

    public function setDeath(?string $death): IEntry
    {
        $this->getLoadedRecord()->death = strval($death);
        return $this;
    }

    public function getDeath(): ?string
    {
        $data = $this->getLoadedRecord()->death;
        return empty($data) ? null : strval($data);
    }

    /**
     * @throws MapperException
     * @throws PedigreeException
     * @return ARecord[]
     */
    public function getChildren(): array
    {
        // unhook the original class, use only definition and create new clear copy
        $record = get_class($this->getLoadedRecord());

        $search = new Search(new $record());
        $search->exact('motherId', strval($this->getLoadedRecord()->id));
        $search->exact('fatherId', strval($this->getLoadedRecord()->id));
        $search->useOr();
        return $search->getResults();
    }

    /**
     * @param int|null $fatherId
     * @param int|null $motherId
     * @throws MapperException
     * @throws PedigreeException
     * @return bool|null
     */
    public function saveFamily(?int $fatherId, ?int $motherId): ?bool
    {
        $willSave = false;
        if (boolval($this->setFatherId($fatherId))) {
            $willSave = true;
        }
        if (boolval($this->setMotherId($motherId))) {
            $willSave = true;
        }
        if ($willSave) {
            return $this->getLoadedRecord()->save();
        }
        return null;
    }

    public function setSuccesses(string $successes): IEntry
    {
        $this->getLoadedRecord()->successes = $successes;
        return $this;
    }

    public function getSuccesses(): string
    {
        return strval($this->getLoadedRecord()->successes);
    }

    public function setSex(string $sex): IEntry
    {
        $this->getLoadedRecord()->sex = $sex;
        return $this;
    }

    public function getSex(): string
    {
        return strval($this->getLoadedRecord()->sex);
    }

    public function setText(string $text): IEntry
    {
        $this->getLoadedRecord()->text = $text;
        return $this;
    }

    public function getText(): string
    {
        return strval($this->getLoadedRecord()->text);
    }

    /**
     * @param string $what
     * @param string|null $sex
     * @throws MapperException
     * @throws PedigreeException
     * @return ARecord[]
     */
    public function getLike(string $what, ?string $sex): array
    {
        return $this->getLoadedRecord()->getLike($what, $sex);
    }
}
