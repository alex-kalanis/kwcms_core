<?php

namespace kalanis\kw_pedigree\Storage\MultiTable;


use kalanis\kw_mapper\Mappers;
use kalanis\kw_mapper\Records\ARecord;


/**
 * Class PedigreeItemMapper
 * @package kalanis\kw_pedigree\Storage\MultiTable
 */
class PedigreeItemMapper extends Mappers\Database\ADatabase
{
    protected function setMap(): void
    {
        $this->setSource('pedigree');
        $this->setTable('kal_pedigree_upd');
        $this->setRelation('id', 'kp_id');
        $this->setRelation('key', 'kp_key');
        $this->setRelation('name', 'kp_name');
        $this->setRelation('kennel', 'kp_kennel');
        $this->setRelation('birth', 'kp_birth');
        $this->setRelation('address', 'kp_address');
        $this->setRelation('trials', 'kp_trials');
        $this->setRelation('photo', 'kp_photo');
        $this->setRelation('photoX', 'kp_photo_x');
        $this->setRelation('photoY', 'kp_photo_y');
        $this->setRelation('breed', 'kp_breed');
        $this->setRelation('sex', 'kp_sex');
        $this->setRelation('blood', 'kp_blood');
        $this->setRelation('text', 'kp_text');
        $this->addPrimaryKey('id');
        $this->addForeignKey('parents', '\kalanis\kw_pedigree\Storage\MultiTable\PedigreeRelateRecord', 'id', 'childId');
        $this->addForeignKey('children', '\kalanis\kw_pedigree\Storage\MultiTable\PedigreeRelateRecord', 'id', 'parentId');
    }

    public function beforeDelete(ARecord $record): bool
    {
        $relation = new PedigreeRelateRecord();
        $relation->childId = $record->id;
        $all = $relation->loadMultiple();
        foreach ($all as $item) {
            $item->delete();
        }
        $relation = new PedigreeRelateRecord();
        $relation->parentId = $record->id;
        $all = $relation->loadMultiple();
        foreach ($all as $item) {
            $item->delete();
        }
        return parent::beforeDelete($record);
    }

    public function getLike(string $what, ?string $sex): array
    {
        $query = 'SELECT `%1$s` AS `id`, `%2$s` AS `key`, `%3$s` AS `name`, `%4$s` AS `kennel` FROM `%5$s` WHERE (`%3$s` LIKE :named1 OR `%4$s` LIKE :named1)';
        $params = [':named1' => sprintf('%%%s%%', $what)];
        if (!is_null($sex)) {
            $query .= ' AND `%6$s` = :sx1';
            $params[':sx1'] = $sex;
        }
        $query .= ' ORDER BY `%4$s` ASC, `%3$s` ASC LIMIT 0, 30;';

        $result = $this->database->query(sprintf($query,
            $this->relations['id'],
            $this->relations['key'],
            $this->relations['name'],
            $this->relations['kennel'],
            $this->getTable(),
            $this->relations['sex']
        ), $params);

        $items = [];
        foreach ($result as $line) {
            $item = new PedigreeItemRecord();
            $item->loadWithData($line);
            $items[] = $item;
        }
        return $items;
    }

}
