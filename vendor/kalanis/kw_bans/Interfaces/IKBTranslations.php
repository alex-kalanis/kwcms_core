<?php

namespace kalanis\kw_bans\Interfaces;


/**
 * Interface IKBTranslations
 * @package kalanis\kw_bans\Interfaces
 * Translations
 */
interface IKBTranslations
{
    public function ikbUnknownType(): string;

    public function ikbUnknownFormat(): string;

    public function ikbDefinedFileNotFound(string $fileName): string;

    public function ikbInvalidNumOfBlocksTooMany(string $knownIp): string;

    public function ikbInvalidNumOfBlocksAmount(string $knownIp): string;
}
