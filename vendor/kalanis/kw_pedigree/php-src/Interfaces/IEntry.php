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

    public function getKeyKey(): string;

    public function getIdKey(): string;

    public function setId(string $id): self;

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

    public function setFatherId(string $fatherId): self;

    public function getFatherId(): string;

    public function setMotherId(string $motherId): self;

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
