<?php

namespace kalanis\kw_pedigree\Storage;


use kalanis\kw_mapper\Records\ARecord;
use kalanis\kw_pedigree\PedigreeException;


/**
 * Class FactoryAdapter
 * @package kalanis\kw_pedigree\Storage\File
 * Which adapter is correct one?
 */
class FactoryAdapter
{
    /**
     * @param ARecord $record
     * @return AEntryAdapter
     * @throws PedigreeException
     */
    public static function getAdapter(ARecord $record): AEntryAdapter
    {
        if ($record instanceof File\PedigreeRecord) {
            return new File\EntryAdapter();
        } elseif ($record instanceof SingleTable\PedigreeRecord) {
            return new SingleTable\EntryAdapter();
        } elseif ($record instanceof MultiTable\PedigreeItemRecord) {
            return new MultiTable\EntryAdapter();
        } else {
            throw new PedigreeException('Unknown record for getting mapper');
        }
    }
}
