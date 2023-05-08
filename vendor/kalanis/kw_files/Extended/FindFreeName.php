<?php

namespace kalanis\kw_files\Extended;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces;
use kalanis\kw_paths\PathsException;


/**
 * Class FindFreeName
 * @package kalanis\kw_files\Extended
 * Work with files - find which name is free for use (either for dir and file)
 */
class FindFreeName
{
    const FREE_NAME_SEPARATOR = '_';

    /** @var Interfaces\IProcessNodes */
    protected $nodes = null;

    public function __construct(Interfaces\IProcessNodes $nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * @param string[] $path
     * @param string $name
     * @param string $suffix
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    public function findFreeName(array $path, string $name, string $suffix): string
    {
        if (!$this->targetExists($path, $name . $suffix)) {
            return $name . $suffix;
        }
        $i = 0;
        while ($this->targetExists($path, $name . $this->getNameSeparator() . strval($i) . $suffix)) {
            $i++;
        }
        return $name . $this->getNameSeparator() . strval($i) . $suffix;
    }

    /**
     * @param array<string> $path
     * @param string $name
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    protected function targetExists(array $path, string $name): bool
    {
        return $this->nodes->exists(array_merge($path, [$name]));
    }

    protected function getNameSeparator(): string
    {
        return static::FREE_NAME_SEPARATOR;
    }
}
