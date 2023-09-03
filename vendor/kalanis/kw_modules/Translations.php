<?php

namespace kalanis\kw_modules;


use kalanis\kw_modules\Interfaces\IMdTranslations;


/**
 * Class Translations
 * @package kalanis\kw_modules
 * Translations
 */
class Translations implements IMdTranslations
{
    public function mdNoLoaderSet(): string
    {
        return 'No loader set!';
    }

    public function mdNoSourceSet(): string
    {
        return 'No source set!';
    }

    /**
     * @param string $classPath
     * @return string
     * @codeCoverageIgnore autoloader problem - got something that is not a class
     */
    public function mdNotInstanceOfIModule(string $classPath): string
    {
        return sprintf('Class *%s* is not instance of IModule - check interface or query', $classPath);
    }

    public function mdNoModuleFound(): string
    {
        return 'No module found';
    }

    public function mdConfPathNotSet(): string
    {
        return 'Path to config is not set!';
    }

    public function mdStorageTargetNotSet(): string
    {
        return 'Site part and then file name is not set!';
    }

    public function mdStorageLoadProblem(): string
    {
        return 'Problem with storage load';
    }

    public function mdStorageSaveProblem(): string
    {
        return 'Problem with storage save';
    }

    public function mdNoOpeningTag(): string
    {
        return 'No opening tag in stack to compare!';
    }

    public function mdNoEndingTag(string $module): string
    {
        return sprintf('No ending tag for module *%s*', $module);
    }
}
