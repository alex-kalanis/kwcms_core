<?php

namespace kalanis\kw_clipr\Loaders;


use kalanis\kw_clipr\Clipr\Useful;
use kalanis\kw_clipr\CliprException;
use kalanis\kw_clipr\Interfaces;
use kalanis\kw_clipr\Tasks\ATask;
use ReflectionException;


/**
 * Class KwLoader
 * @package kalanis\kw_clipr\Tasks
 * Factory for creating tasks/commands from obtained name
 * In reality it runs like autoloader of own
 */
class KwLoader implements Interfaces\ITargetDirs
{
    /** @var array<string, array<string>> */
    protected array $paths = [];

    /**
     * @param array<string, array<string>> $paths
     * @throws CliprException
     */
    public function __construct(array $paths = [])
    {
        foreach ($paths as $namespace => $path) {
            $pt = implode(DIRECTORY_SEPARATOR, $path);
            if (false === $real = realpath($pt)) {
                throw new CliprException(sprintf('Unknown path *%s*!', $pt), Interfaces\IStatuses::STATUS_BAD_CONFIG);
            }
            $this->paths[$namespace] = explode(DIRECTORY_SEPARATOR, $real);
        }
    }

    /**
     * @param string $classFromParam
     * @throws CliprException
     * @throws ReflectionException
     * @return ATask|null
     * For making instances from more than one path
     * Now it's possible to read from different paths as namespace sources
     * Also each class will be loaded only once
     */
    public function getTask(string $classFromParam): ?ATask
    {
        $classPath = $this->removeExt(Useful::sanitizeClass($classFromParam));
        foreach ($this->paths as $namespace => $path) {
            if ($this->containsPath($classPath, $namespace)) {
                $translatedPath = $this->classPathToRealFile($classPath, $namespace);
                $realPath = $this->makeRealFilePath($path, $translatedPath);
                if (is_null($realPath)) {
                    return null;
                }
                require_once $realPath;
                if (!class_exists($classPath)) {
                    // that file contains none wanted class
                    return null;
                }
                $reflection = new \ReflectionClass($classPath);
                if (!$reflection->isInstantiable()) {
                    // cannot initialize the class - abstract one, interface, trait, ...
                    return null;
                }
                $class = $reflection->newInstance();
                if (!$class instanceof ATask) {
                    // the class inside is not an instance of ATask necessary to run
                    throw new CliprException(sprintf('Class *%s* is not instance of ATask - check interface or query.', $classPath), Interfaces\IStatuses::STATUS_LIB_ERROR);
                }
                return $class;
            }
        }
        return null;
    }

    protected function containsPath(string $classPath, string $namespace): bool
    {
        return (0 === mb_strpos($classPath, $namespace));
    }

    protected function removeExt(string $classPath): string
    {
        // remove ext
        $withExt = mb_strripos($classPath, Interfaces\ISources::EXT_PHP);
        return (false !== $withExt)
        && (mb_strlen($classPath) == $withExt + mb_strlen(Interfaces\ISources::EXT_PHP))
            ? mb_substr($classPath, 0, $withExt)
            : $classPath;
    }

    protected function classPathToRealFile(string $classPath, string $namespace): string
    {
        // change slashes
        $classNoExt = strtr($classPath, ['\\' => DIRECTORY_SEPARATOR, '/' => DIRECTORY_SEPARATOR, ':' => DIRECTORY_SEPARATOR]);
        // remove slash from start
        $classNoStartSlash = (DIRECTORY_SEPARATOR == $classNoExt[0]) ? mb_substr($classNoExt, mb_strlen(DIRECTORY_SEPARATOR)) : $classNoExt;
        // rewrite namespace
        return mb_substr($classNoStartSlash, mb_strlen($namespace));
    }

    /**
     * @param string[] $namespacePath
     * @param string $classPath
     * @-throws CliprException
     * @return string|null
     */
    protected function makeRealFilePath(array $namespacePath, string $classPath): ?string
    {
        $setPath = implode(DIRECTORY_SEPARATOR, $namespacePath) . $classPath . Interfaces\ISources::EXT_PHP;
        $realPath = realpath($setPath);
        if (empty($realPath)) {
            return null;
//            throw new CliprException(sprintf('There is problem with path *%s* - it does not exists!', $setPath), Interfaces\IStatuses::STATUS_BAD_CONFIG);
        }
        return $realPath;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }
}
