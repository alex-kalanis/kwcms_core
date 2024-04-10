<?php

namespace kalanis\kw_connect\core;


use kalanis\kw_connect\core\Interfaces\IFilterFactory;
use kalanis\kw_connect\core\Interfaces\IFilterType;
use ReflectionClass;
use ReflectionException;


/**
 * Class AFactory
 * @package kalanis\kw_connect\core
 * Factory Class for accessing filter types
 * Intentionally without PSR-DI dependency
 */
abstract class AFilterFactory implements IFilterFactory
{
    /**
     * In child only fill this map
     * @var array<string, string>
     */
    protected static array $map = [];

    public static function getInstance(): self
    {
        return new static();
    }

    final protected function __construct()
    {
    }

    /**
     * @param string $action
     * @throws ConnectException
     * @return IFilterType
     */
    public function getFilter(string $action): IFilterType
    {
        if (!isset(static::$map[$action])) {
            throw new ConnectException(sprintf('Unknown filter action *%s*!', $action));
        }
        $className = static::$map[$action];
        try {
            /** @var class-string $className */
            $ref = new ReflectionClass($className);
            $lib = $ref->newInstance();
            if (!$lib instanceof IFilterType) {
                throw new ConnectException(sprintf('Bad filter class *%s*!', $className));
            }
            return $lib;
        } catch (ReflectionException $ex) {
            throw new ConnectException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}
