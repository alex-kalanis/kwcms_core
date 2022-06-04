<?php

namespace kalanis\kw_pedigree\Interfaces;


/**
 * Interface IEntry
 * @package kalanis\kw_pedigree\Interfaces
 * Interface about pedigree entry and processed within them
 * Mainly for editing and search
 */
interface IEntry
{
    const SEX_MALE = 'male';
    const SEX_FEMALE = 'female';

    const BREED_NO = 'no';
    const BREED_YES = 'yes';

    /**
     * Under which table key is that key string
     * @return string
     */
    public function getKeyKey(): string;

    public function setKey(string $key): self;

    /**
     * Key string by which it will be accessed
     * @return string
     */
    public function getKey(): string;

    /**
     * Under which key is that PK, usually number or string
     * @return string
     */
    public function getIdKey(): string;

    public function setId(string $id): self;

    /**
     * PK of that record, return as string, might be a number
     * @return string
     */
    public function getId(): string;

    public function getNameKey(): string;

    public function setName(string $name): self;

    public function getName(): string;

    public function getFamilyKey(): string;

    public function setFamily(string $family): self;

    public function getFamily(): string;

    public function getBirthKey(): string;

    public function setBirth(string $birth): self;

    public function getBirth(): string;

    /**
     * @param string $fatherId
     * @return bool|null bool if changed, null if nothing happens
     */
    public function setFatherId(string $fatherId): ?bool;

    public function getFatherId(): string;

    /**
     * @param string $motherId
     * @return bool|null bool if changed, null if nothing happens
     */
    public function setMotherId(string $motherId): ?bool;

    public function getMotherId(): string;

    public function getTrialsKey(): string;

    public function setTrials(string $trials): self;

    public function getTrials(): string;

    public function getBreedKey(): string;

    public function setBreed(string $breed): self;

    public function getBreed(): string;

    public function getSexKey(): string;

    public function setSex(string $sex): self;

    public function getSex(): string;

    public function getTextKey(): string;

    public function setText(string $text): self;

    public function getText(): string;
}
