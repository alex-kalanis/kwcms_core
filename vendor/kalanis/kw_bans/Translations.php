<?php

namespace kalanis\kw_bans;


use kalanis\kw_bans\Interfaces\IKBTranslations;


/**
 * Class Translations
 * @package kalanis\kw_bans
 */
class Translations implements IKBTranslations
{
    public function ikbUnknownType(): string
    {
        return 'Unknown ban type';
    }

    public function ikbUnknownFormat(): string
    {
        return 'Unknown datasource format';
    }

    public function ikbBadIpParsingNoDelimiter(): string
    {
        return 'Delimiter for parsing not set';
    }

    public function ikbDefinedFileNotFound(string $fileName): string
    {
        return 'Defined file was not found';
    }

    public function ikbInvalidNumOfBlocksTooMany(string $knownIp): string
    {
        return 'Invalid IP, too much blocks - ' . $knownIp;
    }

    public function ikbInvalidNumOfBlocksAmount(string $knownIp): string
    {
        return 'Invalid IP, bad amount of blocks - ' . $knownIp;
    }
}
