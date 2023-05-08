<?php

namespace kalanis\kw_mime\Interfaces;


/**
 * Interface IMiTranslations
 * @package kalanis\kw_mime\Interfaces
 * Translations for mime
 */
interface IMiTranslations
{
    public function miCannotLoadFile(string $target): string;

    public function miCannotGetFilePart(string $target): string;

    public function miCannotLoadTempFile(): string;

    public function miNoClass(): string;

    public function miNoMethod(): string;

    public function miNoFunction(): string;

    public function miNoStorage(): string;
}
