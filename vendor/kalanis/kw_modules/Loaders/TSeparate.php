<?php

namespace kalanis\kw_modules\Loaders;


use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Traits\TMdLang;


/**
 * Trait TSeparate
 * @package kalanis\kw_modules\Loaders
 * Separate module name and internal path
 */
trait TSeparate
{
    use TMdLang;

    /**
     * @param string[] $path
     * @param string|null $emptyDefault
     * @throws ModuleException
     * @return string[]
     */
    protected function separateModule(array $path, ?string $emptyDefault = null): array
    {
        if (empty($path)) {
            throw new ModuleException($this->getMdLang()->mdNoModuleFound());
        }

        $target = strval(reset($path));
        $constructPath = is_null($emptyDefault) ? $target : $emptyDefault ;

        if (1 < count($path)) {
            $constructPath = implode('\\', array_slice($path, 1));
        }

        return [$target, $constructPath];
    }
}
