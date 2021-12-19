<?php

namespace kalanis\kw_clipr\Tasks;


use kalanis\kw_clipr\Clipr\Paths;
use kalanis\kw_clipr\CliprException;
use Psr\Container\ContainerInterface;


/**
 * Class TaskFactoryDi
 * @package kalanis\kw_clipr\Tasks
 * Factory for creating tasks/commands from obtained name
 * It contains dependency injection
 * @codeCoverageIgnore because of that internal autoloader
 */
class TaskFactoryDi extends TaskFactory
{
    /** @var ContainerInterface */
    protected $container = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $classPath
     * @return ATask
     * @throws CliprException
     * If you want to add DI, you must preset DI in __construct() and overload this method with DI support
     */
    protected function initTask(string $classPath): ATask
    {
        $paths = Paths::getInstance()->getPaths();
        foreach ($paths as $namespace => $path) {
            if ($this->containsPath($classPath, $namespace)) {
                // PSR lookup
                if ($this->container->has($classPath)) {
                    $task = $this->container->get($classPath);
                    if (!$task instanceof ATask) {
                        throw new CliprException(sprintf('Class *%s* is not instance of ATask - check interface or query', $classPath));
                    }
                    return $task;
                }

                // original lookup for clipr
                $translatedPath = Paths::getInstance()->classToRealFile($classPath, $namespace);
                $realPath = $this->makeRealFilePath($path, $translatedPath);
                require_once $realPath;
                $class = new $classPath();
                return $class;
            }
        }
        throw new CliprException(sprintf('Unknown class *%s* - check name, interface or your config paths.', $classPath));
    }
}
