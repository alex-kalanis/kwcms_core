<?php

namespace kalanis\kw_pedigree\Interfaces;


use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage\APedigreeRecord;


/**
 * Interface ILike
 * @package kalanis\kw_pedigree\Interfaces
 * If that mapper can look for entries with Like
 */
interface ILike
{
    /**
     * @param APedigreeRecord $record
     * @param string $what
     * @param string|null $sex
     * @throws MapperException
     * @throws PedigreeException
     * @return ARecord[]
     */
    public function getLike(APedigreeRecord $record, string $what, ?string $sex = null): array;
}
