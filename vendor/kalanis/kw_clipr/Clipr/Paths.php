<?php

namespace kalanis\kw_clipr\Clipr;


use kalanis\kw_clipr\CliprException;
use kalanis\kw_clipr\Interfaces\ISources;


/**
 * Class Paths
 * @package kalanis\kw_clipr\Clipr
 * Paths to accessing tasks/commands somewhere on volumes
 * It's singleton, because it's passed to different parts of Clipr - loaders and one of modules - and at first needs
 * to be set outside of the system
 */
class Paths
{
    protected static $instance = null;
    protected $paths = [];

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
     * @codeCoverageIgnore why someone would run that?!
     */
    private function __clone()
    {
    }

    /**
     * @param string $namespace
     * @param string $path
     * @return $this
     * @throws CliprException
     */
    public function addPath(string $namespace, string $path): self
    {
        $realPath = realpath($path);
        if (false === $realPath) {
            throw new CliprException(sprintf('Unknown path *%s*!', $path));
        }
        $this->paths[$namespace] = $path;
        return $this;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }

    public function clearPaths(): void
    {
        $this->paths = [];
    }

    public function classToRealFile(string $classPath, string $namespace): string
    {
        // remove ext
        $withExt = mb_strripos($classPath, ISources::EXT_PHP);
        $classNoExt = (false !== $withExt)
        && (mb_strlen($classPath) == $withExt + mb_strlen(ISources::EXT_PHP))
            ? mb_substr($classPath, 0, $withExt)
            : $classPath;
        // change slashes
        $classNoExt = strtr($classNoExt, ['\\' => DIRECTORY_SEPARATOR, '/' => DIRECTORY_SEPARATOR, ':' => DIRECTORY_SEPARATOR]);
        // rewrite namespace
        return mb_substr($classNoExt, mb_strlen($namespace));
    }

    public function realFileToClass(string $dir, string $file): ?string
    {
        $dirLen = mb_strlen($dir);
        foreach ($this->paths as $namespace => $path) {
            // got some path
            $compLen = min($dirLen, mb_strlen($path));
            $lgPath = mb_substr(Useful::mb_str_pad($path, $compLen, '-'), 0, $compLen);
            $lgDir = mb_substr(Useful::mb_str_pad($dir, $compLen, '-'), 0, $compLen);
            if ($lgDir == $lgPath) {
                // rewrite namespace
                $lcDir = DIRECTORY_SEPARATOR == $dir[0] ? $dir : DIRECTORY_SEPARATOR . $dir;
                $end = $namespace . mb_substr($lcDir, $compLen);
                // change slashes
                $namespaced = DIRECTORY_SEPARATOR == mb_substr($end, -1) ? $end : $end . DIRECTORY_SEPARATOR;
                $namespaced = strtr($namespaced, DIRECTORY_SEPARATOR, '\\');
                // remove ext
                $withExt = mb_strripos($file, ISources::EXT_PHP);
                $withoutExt = (false !== $withExt) ? mb_substr($file, 0, $withExt) : $file ;
                // return named class
                return $namespaced . $withoutExt;
            }
        }
        return null;
    }
}
