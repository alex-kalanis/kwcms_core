<?php

namespace kalanis\kw_connect\core;


use kalanis\kw_connect\core\Interfaces\IFilterFactory;
use kalanis\kw_connect\core\Interfaces\IFilterType;


/**
 * Class AFactory
 * @package kalanis\kw_connect\core
 * Factory Class for accessing filter types
 */
abstract class AFilterFactory implements IFilterFactory
{
    /**
     * In child only fill this map
     * @var string[]
     */
    protected static $map = [];

    public static function getInstance(): self
    {
        return new static();
    }

    protected function __construct()
    {
    }

    /**
     * @param string $action
     * @return IFilterType
     * @throws ConnectException
     */
    public function getFilter(string $action): IFilterType
    {
        if (!isset(static::$map[$action])) {
            throw new ConnectException(sprintf('Unknown filter action *%s*!', $action));
        }
        $class = static::$map[$action];
        return new $class();
    }
}
