<?php

namespace kalanis\kw_pedigree\Storage\File;


use kalanis\kw_mapper\Mappers;


/**
 * Class PedigreeMapper
 * @package kalanis\kw_pedigree\Storage\File
 */
class PedigreeMapper extends Mappers\File\ATable
{
    protected function setMap(): void
    {
        $this->setFormat('\kalanis\kw_mapper\Storage\File\Formats\SeparatedElements');
        $this->setRelation('id', 0);
        $this->setRelation('name', 1);
        $this->setRelation('kennel', 2);
        $this->setRelation('birth', 3);
        $this->setRelation('father', 4);
        $this->setRelation('mother', 5);
        $this->setRelation('fatherId', 6);
        $this->setRelation('motherId', 7);
        $this->setRelation('address', 8);
        $this->setRelation('trials', 9);
        $this->setRelation('photo', 10);
        $this->setRelation('photoX', 11);
        $this->setRelation('photoY', 12);
        $this->setRelation('breed', 13);
        $this->setRelation('sex', 14);
        $this->setRelation('blood', 15);
        $this->setRelation('text', 16);
        $this->addPrimaryKey('id');
    }
}
