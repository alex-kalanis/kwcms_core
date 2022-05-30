<?php

namespace kalanis\kw_clipr\Loaders;


use kalanis\kw_clipr\Clipr\Paths;
use kalanis\kw_clipr\Clipr\Useful;
use kalanis\kw_clipr\CliprException;
use kalanis\kw_clipr\Interfaces\ILoader;
use kalanis\kw_clipr\Tasks\ATask;
use Psr\Container\ContainerInterface;


/**
 * Class DiLoader
 * @package kalanis\kw_clipr\Tasks
 * Factory for creating tasks/commands from obtained name
 * It contains dependency injection - so everything loaded is from source targeted by that DI
 * @codeCoverageIgnore because of that internal autoloader
 */
class DiLoader implements ILoader
{
    /** @var ContainerInterface */
    protected $container = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getTask(string $classFromParam): ?ATask
    {
        $classPath = Useful::sanitizeClass($classFromParam);
        $paths = Paths::getInstance()->getPaths();
        foreach ($paths as $namespace => $path) {
            if ($this->containsPath($classPath, $namespace) && $this->container->has($classPath)) {
                $task = $this->container->get($classPath);
                if (!$task instanceof ATask) {
                    throw new CliprException(sprintf('Class *%s* is not instance of ATask - check interface or query', $classPath));
                }
                return $task;
            }
        }
        return null;
    }

    protected function containsPath(string $classPath, string $namespace): bool
    {
        return (0 === mb_strpos($classPath, $namespace));
    }
}
