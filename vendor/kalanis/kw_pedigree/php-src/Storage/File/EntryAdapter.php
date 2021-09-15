<?php

namespace kalanis\kw_pedigree\Storage\File;


use kalanis\kw_pedigree\Storage\AEntryAdapter;


/**
 * Class EntryAdapter
 * @package kalanis\kw_pedigree\Storage\File
 */
class EntryAdapter extends AEntryAdapter
{
    public function getLike(string $what, $sex): array
    {
        // @todo: parse from sources; probably load all and then kick out that unusable
        return $this->record->getMapper()->getLike($what, $sex);
    }
}
