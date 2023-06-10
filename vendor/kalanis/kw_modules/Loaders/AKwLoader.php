<?php

namespace kalanis\kw_modules\Loaders;


use kalanis\kw_autoload\AutoloadException;
use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\ModuleException;


/**
 * Class KwLoader
 * @package kalanis\kw_modules\Loaders
 * Load modules data from defined targets
 * @codeCoverageIgnore contains external autoloader
 *
 * Name is passed as first big and the rest little ( ucfirst(strtolower($x)) )
 * - lookup by curly braces
 *
 * KWCMS is for kw_autoloader on "vendor" position and modules dir on "project" position
 */
abstract class AKwLoader implements ILoader
{
    public function __construct()
    {
        if (class_exists('\kalanis\kw_autoload\Autoload')) {
            \kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%5$s%1$sphp-src%1$s%6$s');
            \kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%5$s%1$ssrc%1$s%6$s');
            \kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%5$s%1$s%6$s');
        }
    }

    public function load(string $module, ?string $constructPath = null, array $constructParams = []): ?IModule
    {
        $constructPath = $constructPath ?: $module ;
        $classPath = $this->getClassName($module, $constructPath);
        try {
            $reflection = new \ReflectionClass($classPath);
        } catch (AutoloadException | \ReflectionException $ex) {
            return null;
        }
        if ($reflection->isInstantiable()) {
            $module = $reflection->newInstanceArgs($constructParams);
            if (!$module instanceof IModule) {
                throw new ModuleException(sprintf('Class *%s* is not instance of IModule - check interface or query', $classPath));
            }
            return $module;
        }
        return null;
    }

    abstract protected function getClassName(string $module, string $constructPath): string;
}
