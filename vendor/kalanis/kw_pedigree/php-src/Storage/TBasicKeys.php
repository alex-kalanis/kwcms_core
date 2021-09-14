<?php

namespace kalanis\kw_pedigree\Storage;


/**
 * Trait TBasicKeys
 * @package kalanis\kw_pedigree\Storage
 * Basic keys as defined in records
 */
trait TBasicKeys
{
    public function getIdKey(): string
    {
        return 'id';
    }

    public function getKeyKey(): string
    {
        return 'key';
    }

    public function getNameKey(): string
    {
        return 'name';
    }

    public function getFamilyKey(): string
    {
        return 'kennel';
    }

    public function getBirthKey(): string
    {
        return 'birth';
    }

    public function getTrialsKey(): string
    {
        return 'trials';
    }

    public function getBreedKey(): string
    {
        return 'breed';
    }

    public function getSexKey(): string
    {
        return 'sex';
    }

    public function getTextKey(): string
    {
        return 'text';
    }
}
