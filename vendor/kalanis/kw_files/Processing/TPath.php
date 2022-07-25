<?php

namespace kalanis\kw_files\Processing;


/**
 * trait TPath
 * @package kalanis\kw_files\Processing
 */
trait TPath
{
    /** @var string */
    protected $path = '';

    public function setPath(string $path = ''): void
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
