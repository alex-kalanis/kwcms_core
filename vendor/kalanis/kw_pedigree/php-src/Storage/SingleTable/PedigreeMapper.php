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
        $this->addForeignKey('father', PedigreeRecord::class, 'fatherId', 'id');
        $this->addForeignKey('mother', PedigreeRecord::class, 'motherId', 'id');
        $this->addPrimaryKey('id');
    }

    public function getLike(string $what, ?string $sex): array
    {
        $query = 'SELECT `%1$s` AS `id`, `%2$s` AS `name`, `%3$s` AS `kennel` FROM `%4$s` WHERE (`%2$s` LIKE :named1 OR `%3$s` LIKE :named1)';
        $params = [':named1' => sprintf('%%%s%%', $what)];
        if (!is_null($sex)) {
            $query .= ' AND `%5$s` = :sx1';
            $params[':sx1'] = $sex;
        }
        $query .= ' ORDER BY `%3$s` ASC, `%2$s` ASC LIMIT 0, 30;';

        $result = $this->getReadDatabase()->query(sprintf($query,
            $this->relations['id'],
            $this->relations['name'],
            $this->relations['kennel'],
            $this->getTable(),
            $this->relations['sex']
        ), $params);

        $items = [];
        foreach ($result as $line) {
            $item = new PedigreeRecord();
            $item->loadWithData($line);
            $items[] = $item;
        }
        return $items;
    }
}
