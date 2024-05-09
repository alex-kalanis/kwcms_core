<?php

namespace kalanis\kw_modules\Loaders\Di;


use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\IMdTranslations;
use kalanis\kw_modules\Interfaces\IModule;
use kalanis\kw_modules\ModuleException;
use kalanis\kw_modules\Traits\TMdLang;
use Psr\Container\ContainerInterface;


/**
 * Class ALoader
 * @package kalanis\kw_modules\Loaders\Di
 * Use Dependency Injection
 * @codeCoverageIgnore contains external autoloader
 *
 * Name is passed as first big and the rest little ( ucfirst(strtolower($x)) )
 * - lookup by curly braces
 */
abstract class ALoader implements ILoader
{
    use TMdLang;

    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container, ?IMdTranslations $lang = null)
    {
        $this->setMdLang($lang);
        $this->container = $container;
    }

    public function load(array $module, array $constructParams = []): ?IModule
    {
        $classPath = $this->getClassName($module);
        if ($this->container->has($classPath)) {
            $module = $this->container->get($classPath);
            if (!$module instanceof IModule) {
                throw new ModuleException($this->getMdLang()->mdNotInstanceOfIModule($classPath));
            }
            return $module;
        }
        return null;
    }

    /**
     * @param string[] $module
     * @throws ModuleException
     * @return string
     */
    abstract protected function getClassName(array $module): string;
}
