<?php

namespace kalanis\kw_modules\Loaders\Di;


use kalanis\kw_modules\Loaders\TSeparate;


/**
 * Class AdminLoader
 * @package kalanis\kw_modules\Loaders\Di
 * Load modules data from defined targets
 * @codeCoverageIgnore contains external autoloader
 *
 * Paths:
 * /modules/{module_name}/php-src/AdminControllers/{module_name}.php as init
 * /modules/{module_name}/php-src/Lib/* as next libraries
 * Namespaces:
 * \KWCMS\modules\{module_name}\AdminControllers\{module_name} as init
 * \KWCMS\modules\{module_name}\Lib\ as next libraries
 *
 * Name is passed as first big and the rest little ( ucfirst(strtolower($x)) )
 * - lookup by curly braces
 */
class AdminLoader extends ALoader
{
    use TSeparate;

    protected function getClassName(array $path): string
    {
        list($target, $constructPath) = $this->separateModule($path);
        return sprintf('\KWCMS\modules\%s\AdminControllers\%s', $target, $constructPath);
    }
}
