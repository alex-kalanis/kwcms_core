<?php

namespace kalanis\kw_pedigree\Storage\File;


use kalanis\kw_mapper\Mappers;
use kalanis\kw_mapper\Storage\Shared\FormatFiles\SeparatedElements;


/**
 * Class PedigreeMapper
 * @package kalanis\kw_pedigree\Storage\File
 */
class PedigreeMapper extends Mappers\File\ATable
{
    protected function setMap(): void
    {
        $this->setFormat(SeparatedElements::class);
        $this->setRelation('id', 0);
        $this->setRelation('key', 1);
        $this->setRelation('name', 2);
        $this->setRelation('kennel', 3);
        $this->setRelation('birth', 4);
        $this->setRelation('father', 5);
        $this->setRelation('mother', 6);
        $this->setRelation('fatherId', 7);
        $this->setRelation('motherId', 8);
        $this->setRelation('address', 9);
        $this->setRelation('trials', 10);
        $this->setRelation('photo', 11);
        $this->setRelation('photoX', 12);
        $this->setRelation('photoY', 13);
        $this->setRelation('breed', 14);
        $this->setRelation('sex', 15);
        $this->setRelation('blood', 16);
        $this->setRelation('text', 17);
        $this->addPrimaryKey('id');
    }
}
