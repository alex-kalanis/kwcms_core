<?php

namespace kalanis\kw_files\Processing;


/**
 * trait TNameFinder
 * @package kalanis\kw_files\Processing
 * Find free name for file - not parsing like usual file
 */
trait TNameFinder
{
    public function findFreeName(array $name, string $suffix): string
    {
        if (!$this->targetExists($name, $suffix)) {
            return $name . $suffix;
        }
        $i = 0;
        while ($this->targetExists($name, $this->getSeparator() . $i . $suffix)) {
            $i++;
        }
        return $name . $this->getSeparator() . $i . $suffix;
    }

    abstract protected function getSeparator(): string;

    abstract protected function targetExists(array $path, string $added): bool;
}
