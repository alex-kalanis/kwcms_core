<?php

namespace kalanis\kw_pedigree\Storage;


use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Records\ASimpleRecord;
use kalanis\kw_pedigree\Interfaces\ILike;
use kalanis\kw_pedigree\PedigreeException;


/**
 * Class APedigreeRecord
 * @package kalanis\kw_pedigree\Storage
 * Shared abstract class necessary for correct access to data
 * @property int $id
 * @property string $name
 * @property string $short
 * @property string $family
 * @property string $birth
 * @property string $death
 * @property string $successes
 * @property string $sex
 * @property string $text
 */
abstract class APedigreeRecord extends ASimpleRecord
{
    /**
     * @param string $what
     * @param string|null $sex
     * @throws MapperException
     * @throws PedigreeException
     * @return APedigreeRecord[]
     */
    public function getLike(string $what, ?string $sex = null): array
    {
        $mapper = $this->getMapper();
        if ($mapper instanceof ILike) {
            return $mapper->getLike($this, $what, $sex);
        }
        throw new PedigreeException('Cannot get entries from source like that');
    }
}
