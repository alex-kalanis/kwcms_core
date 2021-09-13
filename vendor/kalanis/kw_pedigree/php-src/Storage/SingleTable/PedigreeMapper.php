<?php

namespace kalanis\kw_pedigree\Storage\SingleTable;


use kalanis\kw_mapper\Mappers;


/**
 * Class PedigreeMapper
 * @package kalanis\kw_pedigree\Storage\SingleTable
 */
class PedigreeMapper extends Mappers\Database\ADatabase
{
    protected function setMap(): void
    {
        $this->setSource('pedigree');
        $this->setTable('kal_pedigree');
        $this->setRelation('id', 'id');
        $this->setRelation('name', 'name');
        $this->setRelation('kennel', 'kennel');
        $this->setRelation('birth', 'birth');
        $this->setRelation('father', 'father');
        $this->setRelation('mother', 'mother');
        $this->setRelation('fatherId', 'father_id');
        $this->setRelation('motherId', 'mother_id');
        $this->setRelation('address', 'address');
        $this->setRelation('trials', 'trials');
        $this->setRelation('photo', 'photo');
        $this->setRelation('photoX', 'photo_x');
        $this->setRelation('photoY', 'photo_y');
        $this->setRelation('breed', 'breed');
        $this->setRelation('sex', 'sex');
        $this->setRelation('blood', 'blood');
        $this->setRelation('text', 'text');
        $this->addPrimaryKey('id');
    }
}
