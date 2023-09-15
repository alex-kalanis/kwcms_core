<?php

namespace kalanis\kw_modules\Loaders\Kw;


use kalanis\kw_autoload\AutoloadException;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\IMdTranslations;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Traits\TMdLang;


/**
 * Class AKwLoader
 * @package kalanis\kw_modules\Loaders\Kw
 * Load modules data from defined targets
 * @codeCoverageIgnore contains external autoloader
 *
 * Name is passed as first big and the rest little ( ucfirst(strtolower($x)) )
 * - lookup by curly braces
 *
 * KWCMS is for kw_autoloader on "vendor" position and modules dir on "project" position
 */
abstract class ALoader implements ILoader
{
    use TMdLang;

    public function __construct(?IMdTranslations $lang = null)
    {
        if (class_exists('\kalanis\kw_autoload\Autoload')) {
            \kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%5$s%1$sphp-src%1$s%6$s');
            \kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%5$s%1$ssrc%1$s%6$s');
            \kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%5$s%1$s%6$s');
        }
        $this->setMdLang($lang);
    }

    public function load(array $module, array $constructParams = []): ?IModule
    {
        /** @var class-string<IModule> $classPath */
        $classPath = $this->getClassName($module);
        try {
            $reflection = new \ReflectionClass($classPath);
            $module = $reflection->newInstanceArgs($constructParams);
            if (!$module instanceof IModule) {
                throw new ModuleException($this->getMdLang()->mdNotInstanceOfIModule($classPath));
            }
            return $module;
        } catch (AutoloadException | \ReflectionException $ex) {
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
