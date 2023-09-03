<?php

namespace kalanis\kw_modules\Loaders;


/**
 * Class KwApiLoader
 * @package kalanis\kw_modules\Loaders
 * Load modules data from defined targets
 * @codeCoverageIgnore contains external autoloader
 *
 * Paths:
 * /modules/{module_name}/php-src/ApiControllers/{module_name}.php as init
 * /modules/{module_name}/php-src/Lib/* as next libraries
 * Namespaces:
 * \KWCMS\modules\{module_name}\ApiControllers\{module_name} as init
 * \KWCMS\modules\{module_name}\Lib\ as next libraries
 *
 * Name is passed as first big and the rest little ( ucfirst(strtolower($x)) )
 * - lookup by curly braces
 */
class KwApiLoader extends AKwLoader
{
    use TSeparate;

    protected function getClassName(array $path): string
    {
        list($target, $constructPath) = $this->separateModule($path);
        return sprintf('\KWCMS\modules\%s\ApiControllers\%s', $target, $constructPath);
    }
}
