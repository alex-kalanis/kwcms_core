<?php

namespace kalanis\kw_paths;


/**
 * Class ArrayPath
 * @package kalanis\kw_paths
 * Path as an array
 * Each level (as usually seen as directory) is an extra position in path
 * The last one is usually the name
 */
class ArrayPath
{
    /** @var string[] */
    protected $path = [];

    /**
     * @throws PathsException
     * @return string
     */
    public function __toString()
    {
        return $this->getString();
    }

    /**
     * @param string $path
     * @throws PathsException
     * @return $this
     */
    public function setString(string $path): self
    {
        $this->path = array_filter(array_filter(Stuff::pathToArray($path), [Stuff::class, 'notDots']));
        return $this;
    }

    /**
     * @param string[] $path
     * @return $this
     */
    public function setArray(array $path): self
    {
        $this->path = array_filter(array_filter($path, [Stuff::class, 'notDots']));
        return $this;
    }

    /**
     * @throws PathsException
     * @return string
     */
    public function getString(): string
    {
        return Stuff::arrayToPath($this->path);
    }

    /**
     * @return string[]
     */
    public function getArray(): array
    {
        return array_merge($this->path, []); // remove indexes
    }

    /**
     * @throws PathsException
     * @return string
     */
    public function getStringDirectory(): string
    {
        $array = $this->getArrayDirectory();
        return empty($array)
            ? ''
            : Stuff::arrayToPath($array)
        ;
    }

    /**
     * @return string[]
     */
    public function getArrayDirectory(): array
    {
        return (1 < count($this->path))
            ? array_slice($this->path, 0, -1)
            : []
        ;
    }

    public function getFileName(): string
    {
        return (0 < count($this->path))
            ? end($this->path)
            : ''
        ;
    }
}
