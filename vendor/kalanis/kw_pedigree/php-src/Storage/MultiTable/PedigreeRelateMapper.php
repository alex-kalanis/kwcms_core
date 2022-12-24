<?php

namespace kalanis\kw_pedigree\Storage\MultiTable;


use kalanis\kw_mapper\Mappers;


/**
 * Class PedigreeRelateMapper
 * @package kalanis\kw_pedigree\Storage\MultiTable
 */
class PedigreeRelateMapper extends Mappers\Database\ADatabase
{
    protected function setMap(): void
    {
        $this->setSource('pedigree');
        $this->setTable('kal_pedigree_relate');
        $this->setRelation('id', 'kpr_id');
        $this->setRelation('childId', 'kp_id_child');
        $this->setRelation('parentId', 'kp_id_parent');
        $this->addPrimaryKey('id');
        $this->addForeignKey('parents', PedigreeItemRecord::class, 'parentId', 'id');
        $this->addForeignKey('children', PedigreeItemRecord::class, 'childId', 'id');
    }
}
