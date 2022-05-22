<?php

namespace kalanis\kw_bans\Sources;


use kalanis\kw_bans\BanException;
use kalanis\kw_bans\Interfaces\IKBTranslations;
use kalanis\kw_bans\Translations;


/**
 * Class File
 * @package kalanis\kw_bans\Sources
 * Bans source is file
 */
class File extends ASources
{
    /**
     * @param string $file
     * @param IKBTranslations|null $lang
     * @throws BanException
     */
    public function __construct(string $file, ?IKBTranslations $lang = null)
    {
        $lang = $lang ?: new Translations();
        $rows = @file($file);
        if (false === $rows) {
            throw new BanException($lang->ikbDefinedFileNotFound($file));
        }

        // remove empty records
        $rows = array_filter($rows);

        // sort them, better for lookup
        sort($rows);

        // last clearing
        $this->knownRecords = array_map(function ($row) {
            return strtr($row, ["\r" => "", "\n" => ""]);
        }, $rows);

    }
}
