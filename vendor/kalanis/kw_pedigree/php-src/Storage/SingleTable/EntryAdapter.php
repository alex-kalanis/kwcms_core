<?php

namespace kalanis\kw_pedigree\Storage\SingleTable;


use kalanis\kw_pedigree\Interfaces\IEntry;
use kalanis\kw_pedigree\Storage\AEntryAdapter;


/**
 * Class EntryAdapter
 * @package kalanis\kw_pedigree\Storage\SingleTable
 */
class EntryAdapter extends AEntryAdapter
{
    public function setKey(string $key): IEntry
    {
        $this->record->id = $key;
        return $this;
    }

    public function getKey(): string
    {
        return strval($this->record->id);
    }

    public function getKeyKey(): string
    {
        return 'id';
    }
}
