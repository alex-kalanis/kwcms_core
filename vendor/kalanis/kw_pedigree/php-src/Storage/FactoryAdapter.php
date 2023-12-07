<?php

namespace kalanis\kw_pedigree\Storage;


use kalanis\kw_pedigree\PedigreeException;


/**
 * Class FactoryAdapter
 * @package kalanis\kw_pedigree\Storage\File
 * Which adapter is correct one?
 */
class FactoryAdapter
{
    /**
     * @param APedigreeRecord $record
     * @throws PedigreeException
     * @return AEntryAdapter
     */
    public static function getAdapter(APedigreeRecord $record): AEntryAdapter
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
