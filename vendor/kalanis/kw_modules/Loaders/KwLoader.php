<?php

namespace kalanis\kw_modules\Loaders;


use kalanis\kw_modules\Interfaces\ILoader;
use kalanis\kw_modules\Interfaces\IModule;


/**
 * Class KwLoader
 * @package kalanis\kw_modules
 * Load modules data from defined targets
 * @codeCoverageIgnore contains external autoloader
 *
 * Paths:
 * /modules/{module_name}/php-src/{module_name}.php as init
 * /modules/{module_name}/php-src/Lib/* as next libraries
 * Namespaces:
 * \KWCMS\modules\{module_name}\{module_name} as init
 * \KWCMS\modules\{module_name}\Lib\ as next libraries
 *
 * Name is passed as first big and the rest little ( ucfirst(strtolower($x)) )
 * - vyhledavani je podle chlupatych zavorek
 *
 * KWCMS is for kw_autoloader on "vendor" position and modules dir on "project" position
 *
 */
class KwLoader implements ILoader
{
    public function __construct()
    {
        if (class_exists('\kalanis\kw_autoload\Autoload')) {
            \kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%5$s%1$sphp-src%1$s%6$s');
            \kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%5$s%1$ssrc%1$s%6$s');
            \kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%5$s%1$s%6$s');
        }
    }

    public function load(string $module, ?string $constructPath = null, array $constructParams = []): IModule
    {
        $constructPath = $constructPath ?: $module ;
        $className = sprintf('\KWCMS\modules\%s\%s', $module, $constructPath);
        return new $className(...$constructParams);
    }
}
