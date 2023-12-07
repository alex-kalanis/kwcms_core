<?php

namespace kalanis\kw_pedigree\Storage\File;


use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Mappers;
use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_mapper\Storage\Shared;
use kalanis\kw_pedigree\Interfaces\ILike;
use kalanis\kw_pedigree\Interfaces\ISex;
use kalanis\kw_pedigree\Storage\APedigreeRecord;


/**
 * Class PedigreeMapper
 * @package kalanis\kw_pedigree\Storage\File
 */
class PedigreeMapper extends Mappers\Storage\ATable implements ILike
{
    protected function setMap(): void
    {
        $this->setSource('pedigree.txt');
        $this->setStorage();
        $this->setFormat(Shared\FormatFiles\SeparatedElements::class);
        $this->setRelation('id', 0);
        $this->setRelation('short', 1);
        $this->setRelation('name', 2);
        $this->setRelation('family', 3);
        $this->setRelation('birth', 4);
        $this->setRelation('death', 5);
        $this->setRelation('fatherId', 6);
        $this->setRelation('motherId', 7);
        $this->setRelation('successes', 8);
        $this->setRelation('sex', 9);
        $this->setRelation('text', 10);
        $this->addPrimaryKey('id');
    }

    /**
     * @param ARecord $record
     * @throws MapperException
     * @return bool
     */
    protected function beforeSave(ARecord $record): bool
    {
        $short = strval($record->__get('short'));
        $id = intval($record->__get('id'));
        if (!empty($id) && empty($short)) {
            // probably update
            return true;
        }
        if (empty($short)) {
            return false;
        }

        $clear = get_class($record);
        $search = new Search(new $clear());
        $search->exact('short', $short);
        $results = $search->getResults();
        if (empty($results)) {
            return true;
        }
        $result = reset($results);

        return (intval($record->__get('id')) == intval($result->__get('id')));
    }

    /**
     * @param APedigreeRecord $record
     * @param string $what
     * @param string|null $sex
     * @throws MapperException
     * @return APedigreeRecord[]
     */
    public function getLike(APedigreeRecord $record, string $what, ?string $sex = null): array
    {
        // must be this way because files does not support OR
        $search1 = new Search($record);
        $search1->like('name', $what);
        $records1 = $search1->getResults();

        $search2 = new Search($record);
        $search2->like('family', $what);
        $records2 = $search2->getResults();

        $records = (array) array_combine(array_map([$this, 'getResultId'], $records1), $records1)
            + array_combine(array_map([$this, 'getResultId'], $records2), $records2);

        if ($sex && in_array($sex, [ISex::MALE, ISex::FEMALE])) {
            $limited = [];
            foreach ($records as $record) {
                if ($record->__get('sex') == $sex) {
                    $limited[] = $record;
                }
            }
            $records = $limited;
        }
        return $records;
    }

    public function getResultId(APedigreeRecord $record): int
    {
        return intval($record->id);
    }
}
