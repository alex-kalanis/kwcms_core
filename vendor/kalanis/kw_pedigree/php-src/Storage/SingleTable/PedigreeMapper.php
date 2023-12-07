<?php

namespace kalanis\kw_pedigree\Storage\SingleTable;


use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Mappers;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_pedigree\Interfaces\ILike;
use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage\APedigreeRecord;


/**
 * Class PedigreeMapper
 * @package kalanis\kw_pedigree\Storage\SingleTable
 * Need to be used over MySQL / MariaDB
 */
class PedigreeMapper extends Mappers\Database\ADatabase implements ILike
{
    protected function setMap(): void
    {
        $this->setSource('pedigree');
        $this->setTable('kw_pedigree');
        $this->setRelation('id', 'pedigree_id');
        $this->setRelation('short', 'pedigree_short');
        $this->setRelation('name', 'pedigree_name');
        $this->setRelation('family', 'pedigree_family');
        $this->setRelation('birth', 'pedigree_birth');
        $this->setRelation('death', 'pedigree_death');
        $this->setRelation('fatherId', 'pedigree_father_id');
        $this->setRelation('motherId', 'pedigree_mother_id');
        $this->setRelation('successes', 'pedigree_successes');
        $this->setRelation('sex', 'pedigree_sex');
        $this->setRelation('text', 'pedigree_text');
        $this->addForeignKey('father', PedigreeRecord::class, 'fatherId', 'id');
        $this->addForeignKey('mother', PedigreeRecord::class, 'motherId', 'id');
        $this->addPrimaryKey('id');
    }

    /**
     * @param ARecord $record
     * @throws MapperException
     * @return bool
     */
    protected function beforeSave(ARecord $record): bool
    {
        $text = strval($record->__get('text'));
        if (empty($text)) {
            $record->__set('text', '');
        }

        $short = strval($record->__get('short'));
        $id = intval($record->__get('id'));
        if (!empty($id) && empty($short)) {
            // probably update
            return true;
        }
        if (empty($short)) {
            return false;
        }
        return true;
    }

    public function getLike(APedigreeRecord $record, string $what, ?string $sex = null): array
    {
        $query = 'SELECT `%1$s` AS `id`, `%2$s` AS `name`, `%3$s` AS `family` FROM `%4$s` WHERE (`%2$s` LIKE :named1 OR `%3$s` LIKE :named1)';
        $params = [':named1' => sprintf('%%%s%%', $what)];
        if (!is_null($sex)) {
            $query .= ' AND `%5$s` = :sx1';
            $params[':sx1'] = $sex;
        }
        $query .= ' ORDER BY `%3$s` ASC, `%2$s` ASC LIMIT 0, 30;';

        $read = $this->getReadDatabase();
        if (method_exists($read, 'query')) {
            $result = $read->query(sprintf($query,
                $this->relations['id'],
                $this->relations['name'],
                $this->relations['family'],
                $this->getTable(),
                $this->relations['sex']
            ), $params);

            $items = [];
            foreach ($result as $line) {
                $item = clone $record;
                $item->loadWithData($line);
                $items[] = $item;
            }
            return $items;
        } else {
            // @codeCoverageIgnoreStart
            // when you set the DB which does not have a query method (LDAP)
            throw new PedigreeException('This mapper does not have query!');
        }
        // @codeCoverageIgnoreEnd
    }
}
