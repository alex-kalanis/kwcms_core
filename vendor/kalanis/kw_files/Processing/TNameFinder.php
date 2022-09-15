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
     * @param array<string> $name
     * @param string $suffix
     * @return string
     */
    public function findFreeName(array $name, string $suffix): string
    {
        if (!$this->targetExists($name, $suffix)) {
            return $this->compactName($name) . $suffix;
        }
        $i = 0;
        while ($this->targetExists($name, $this->getNameSeparator() . strval($i) . $suffix)) {
            $i++;
        }
        return $this->compactName($name) . $this->getNameSeparator() . strval($i) . $suffix;
    }

    abstract protected function getNameSeparator(): string;

    /**
     * @param array<string> $path
     * @param string $added
     * @return bool
     */
    abstract protected function targetExists(array $path, string $added): bool;

    /**
     * @param array<string> $path
     * @param string $pathDelimiter
     * @return string
     */
    abstract public function compactName(array $path, string $pathDelimiter = DIRECTORY_SEPARATOR): string;
}
