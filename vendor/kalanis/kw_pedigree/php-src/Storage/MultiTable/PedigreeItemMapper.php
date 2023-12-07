<?php

namespace kalanis\kw_pedigree\Storage\MultiTable;


use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Mappers;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_pedigree\Interfaces\ILike;
use kalanis\kw_pedigree\PedigreeException;
use kalanis\kw_pedigree\Storage\APedigreeRecord;


/**
 * Class PedigreeItemMapper
 * @package kalanis\kw_pedigree\Storage\MultiTable
 * Need to be used over MySQL / MariaDB
 */
class PedigreeItemMapper extends Mappers\Database\ADatabase implements ILike
{
    protected function setMap(): void
    {
        $this->setSource('pedigree');
        $this->setTable('kw_pedigree_upd');
        $this->setRelation('id', 'kwp_id');
        $this->setRelation('short', 'kwp_short');
        $this->setRelation('name', 'kwp_name');
        $this->setRelation('family', 'kwp_family');
        $this->setRelation('birth', 'kwp_birth');
        $this->setRelation('death', 'kwp_death');
        $this->setRelation('successes', 'kwp_successes');
        $this->setRelation('sex', 'kwp_sex');
        $this->setRelation('text', 'kwp_text');
        $this->addPrimaryKey('id');
        $this->addForeignKey('parents', $this->getRelateRecordClass(), 'id', 'childId');
        $this->addForeignKey('children', $this->getRelateRecordClass(), 'id', 'parentId');
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

    /**
     * @param ARecord|PedigreeItemRecord $record
     * @throws MapperException
     * @return bool
     */
    public function beforeDelete(ARecord $record): bool
    {
        $relateRecordClass = $this->getRelateRecordClass();
        $relation = new $relateRecordClass();
        /** @var PedigreeRelateRecord $relation */
        $relation->childId = intval(strval($record->__get('id')));
        $all = $relation->loadMultiple();
        foreach ($all as $item) {
            $item->delete();
        }
        $relation = new $relateRecordClass();
        /** @var PedigreeRelateRecord $relation */
        $relation->parentId = intval(strval($record->__get('id')));
        $all = $relation->loadMultiple();
        foreach ($all as $item) {
            $item->delete();
        }
        return parent::beforeDelete($record);
    }

    /**
     * @return string
     * @codeCoverageIgnore used another one for testing
     */
    protected function getRelateRecordClass(): string
    {
        return PedigreeRelateRecord::class;
    }

    public function getLike(APedigreeRecord $record, string $what, ?string $sex = null): array
    {
        $query = 'SELECT `%1$s` AS `id`, `%2$s` AS `short`, `%3$s` AS `name`, `%4$s` AS `family` FROM `%5$s` WHERE (`%3$s` LIKE :named1 OR `%4$s` LIKE :named1)';
        $params = [':named1' => sprintf('%%%s%%', $what)];
        if (!is_null($sex)) {
            $query .= ' AND `%6$s` = :sx1';
            $params[':sx1'] = $sex;
        }
        $query .= ' ORDER BY `%4$s` ASC, `%3$s` ASC LIMIT 0, 30;';

        $read = $this->getReadDatabase();
        if (method_exists($read, 'query')) {
            $result = $read->query(sprintf($query,
                $this->relations['id'],
                $this->relations['short'],
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
