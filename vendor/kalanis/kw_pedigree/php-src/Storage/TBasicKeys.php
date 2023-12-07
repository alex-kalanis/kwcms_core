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

    public function getShortKey(): string
    {
        return 'short';
    }

    public function getNameKey(): string
    {
        return 'name';
    }

    public function getFamilyKey(): string
    {
        return 'family';
    }

    public function getBirthKey(): string
    {
        return 'birth';
    }

    public function getDeathKey(): string
    {
        return 'death';
    }

    public function getSuccessesKey(): string
    {
        return 'successes';
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
