<?php

namespace kalanis\kw_storage\Storage\Target;


use kalanis\kw_storage\Interfaces;
use kalanis\kw_storage\StorageException;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\kw_storage\Storage\Target
 * Simple example of storage factory
 */
class Factory
{
    /** @var array<string, class-string<Interfaces\ITarget>|null> */
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
     * @param object|array<string, string|object>|string|null $params
     * @throws StorageException
     * @return Interfaces\ITarget|null storage adapter or empty for no storage set
     */
    public function getStorage($params): ?Interfaces\ITarget
    {
        if ($params instanceof Interfaces\ITarget) {
            return $params;
        }

        try {
            if (is_array($params)) {
                if (isset($params['storage'])) {
                    if (is_object($params['storage'])) {
                        if ($params['storage'] instanceof Interfaces\ITarget) {
                            return $params['storage'];
                        } else {
                            return null;
                        }
                    }
                    $lang = (isset($params['lang']) && is_object($params['lang']) && ($params['lang'] instanceof Interfaces\IStTranslations))
                        ? $params['lang']
                        : null;
                    return $this->fromPairs(strval($params['storage']), $lang);
                }
            }

            if (is_string($params)) {
                return $this->fromPairs(strval($params));
            }

            return null;

        } catch (ReflectionException $ex) {
            throw new StorageException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * @param string $name
     * @param Interfaces\IStTranslations|null $lang
     * @throws ReflectionException
     * @return Interfaces\ITarget|null
     */
    protected function fromPairs(string $name, ?Interfaces\IStTranslations $lang = null): ?Interfaces\ITarget
    {
        if (isset(static::$pairs[$name])) {
            $class = static::$pairs[$name];
            if (is_string($class)) {
                $reflection = new \ReflectionClass($class);
                if ($reflection->isInstantiable()) {
                    $obj = $reflection->newInstance($lang);
                    if ($obj instanceof Interfaces\ITarget) {
                        return $obj;
                    }
                }
            }
        }
        return null;
    }
}
