<?php

namespace kalanis\kw_files\Processing;


/**
 * trait TNameFinder
 * @package kalanis\kw_files\Processing
 * Find free name for file - not parsing like usual file
 */
trait TNameFinder
{
    /**
     * @param string[] $path
     * @param string $name
     * @param string $suffix
     * @return string
     */
    public function findFreeName(array $path, string $name, string $suffix): string
    {
        $fullPath = array_merge($path, [$name]);
        if (!$this->targetExists($fullPath, $suffix)) {
            return $name . $suffix;
        }
        $i = 0;
        while ($this->targetExists($fullPath, $this->getNameSeparator() . strval($i) . $suffix)) {
            $i++;
        }
        return $name . $this->getNameSeparator() . strval($i) . $suffix;
    }

    abstract protected function getNameSeparator(): string;

    /**
     * @param array<string> $path
     * @param string $added
     * @return bool
     */
    abstract protected function targetExists(array $path, string $added): bool;
}
