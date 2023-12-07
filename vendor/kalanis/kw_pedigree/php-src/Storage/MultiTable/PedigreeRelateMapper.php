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
        $this->setTable('kw_pedigree_relate');
        $this->setRelation('id', 'kwpr_id');
        $this->setRelation('childId', 'kwp_id_child');
        $this->setRelation('parentId', 'kwp_id_parent');
        $this->addPrimaryKey('id');
        $this->addForeignKey('parents', PedigreeItemRecord::class, 'parentId', 'id');
        $this->addForeignKey('children', PedigreeItemRecord::class, 'childId', 'id');
    }
}
