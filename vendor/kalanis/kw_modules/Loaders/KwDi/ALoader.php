<?php

namespace kalanis\kw_modules\Loaders\KwDi;


use kalanis\kw_autoload\DependencyInjection;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\IMdTranslations;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Traits\TMdLang;
use ReflectionException;


/**
 * Class ALoader
 * @package kalanis\kw_modules\Loaders\KwDi
 * Load modules data from defined targets
 * @codeCoverageIgnore contains external autoloader
 *
 * Load with DI from kw_autoloader
 */
abstract class ALoader implements ILoader
{
    use TMdLang;

    public function __construct(?IMdTranslations $lang = null)
    {
        $this->setMdLang($lang);
    }

    public function load(array $module, array $constructParams = []): ?IModule
    {
        $classPath = $this->getClassName($module);
        try {
            $di = DependencyInjection::getInstance();
            if (!$module = $di->getRep($classPath)) {
                $module = $di->initClass($classPath, $constructParams);
            }
            if (empty($module)) {
                return null;
            }
            if (!$module instanceof IModule) {
                throw new ModuleException($this->getMdLang()->mdNotInstanceOfIModule($classPath));
            }
            return $module;
        } catch (ReflectionException $ex) {
            return null;
        }
    }

    /**
     * @param string[] $module
     * @throws ModuleException
     * @return string
     */
    abstract protected function getClassName(array $module): string;
}
