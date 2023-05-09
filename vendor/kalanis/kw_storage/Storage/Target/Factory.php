<?php

namespace kalanis\kw_storage\Storage\Target;


use kalanis\kw_storage\Interfaces;


/**
 * Class Factory
 * @package kalanis\kw_storage\Storage\Target
 * Simple example of storage factory
 */
class Factory
{
    /** @var array<string, string|null> */
    protected static $pairs = [
        'mem' => Memory::class,
        'memory' => Memory::class,
        'vol' => Volume::class,
        'volume' => Volume::class,
        'volume::flat' => VolumeTargetFlat::class,
        'volume::stream' => VolumeStream::class,
        'local' => Volume::class,
        'local::flat' => VolumeTargetFlat::class,
        'local::stream' => VolumeStream::class,
        'drive' => Volume::class,
        'drive::flat' => VolumeTargetFlat::class,
        'drive::stream' => VolumeStream::class,
        'none' => null,
    ];

    /**
     * @param mixed|object|array|string|null $params
     * @return Interfaces\ITarget|null storage adapter or empty for no storage set
     */
    public function getStorage($params): ?Interfaces\ITarget
    {
        if ($params instanceof Interfaces\ITarget) {
            return $params;
        }

        if (is_array($params)) {
            if (isset($params['storage'])) {
                return $this->fromPairs(strval($params['storage']));
            }
        }

        if (is_string($params)) {
            return $this->fromPairs(strval($params));
        }
        return null;
    }

    protected function fromPairs(string $name): ?Interfaces\ITarget
    {
        if (isset(static::$pairs[$name])) {
            $class = static::$pairs[$name];
            if (is_string($class)) {
                $obj = new $class();
                if ($obj instanceof Interfaces\ITarget) {
                    return $obj;
                }
            }
        }
        return null;
    }
}
