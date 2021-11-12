<?php

namespace kalanis\kw_connect\Filters;


use kalanis\kw_connect\Interfaces\IFilterFactory;
use kalanis\kw_connect\Interfaces\IFilterType;
use kalanis\kw_mapper\MapperException;


/**
 * Class AFactory
 * @package kalanis\kw_connect\Filters
 * Factory Class for accessing filter types
 */
abstract class AFilterFactory implements IFilterFactory
{
    /**
     * In child only fill this map
     * @var string[]
     */
    protected static $map = [];

    protected static $instance = null;

    public static function getInstance(): self
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    protected function __construct()
    {
    }

    /**
     * @param string $action
     * @return IFilterType
     * @throws MapperException
     */
    public function getFilter(string $action): IFilterType
    {
        if (!isset(static::$map[$action])) {
            throw new MapperException(sprintf('Unknown filter action *%s*!', $action));
        }
        $class = static::$map[$action];
        return new $class();
    }
}
