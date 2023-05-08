<?php

namespace kalanis\kw_mime;


use kalanis\kw_mime\Interfaces\IMiTranslations;


/**
 * Class Translations
 * @package kalanis\kw_mime
 * Translations
 */
class Translations implements IMiTranslations
{
    public function miCannotLoadFile(string $target): string
    {
        return 'Cannot load wanted file.';
    }

    /**
     * @return string
     * @codeCoverageIgnore failing local device
     */
    public function miCannotLoadTempFile(): string
    {
        return 'Cannot load temporary file.';
    }

    /**
     * @param string $target
     * @return string
     * @codeCoverageIgnore failing streams
     */
    public function miCannotGetFilePart(string $target): string
    {
        return 'Cannot extract part of content';
    }

    /**
     * @return string
     * @codeCoverageIgnore failing libraries
     */
    public function miNoClass(): string
    {
        return 'No necessary class defined and available!';
    }

    /**
     * @return string
     * @codeCoverageIgnore failing libraries
     */
    public function miNoMethod(): string
    {
        return 'No necessary method defined and available!';
    }

    /**
     * @return string
     * @codeCoverageIgnore failing libraries
     */
    public function miNoFunction(): string
    {
        return 'No necessary function defined and available!';
    }

    public function miNoStorage(): string
    {
        return 'No storage set!';
    }
}
