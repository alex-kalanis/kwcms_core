<?php

namespace kalanis\kw_modules\Interfaces;


/**
 * Interface IMdTranslations
 * @package kalanis\kw_modules\Interfaces
 * Translations
 */
interface IMdTranslations
{
    public function mdNoLoaderSet(): string;

    public function mdNoSourceSet(): string;

    public function mdNotInstanceOfIModule(string $classPath): string;

    public function mdNoModuleFound(): string;

    public function mdConfPathNotSet(): string;

    public function mdStorageTargetNotSet(): string;

    public function mdStorageLoadProblem(): string;

    public function mdStorageSaveProblem(): string;

    public function mdNoOpeningTag(): string;

    public function mdNoEndingTag(string $module): string;
}
