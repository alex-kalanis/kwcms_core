<?php

namespace kalanis\kw_files\Traits;


use kalanis\kw_files\FilesException;


/**
 * trait TCheckModes
 * @package kalanis\kw_files\Traits
 */
trait TCheckModes
{
    use TLang;

    /**
     * @param int<0, max> $mode
     * @throws FilesException
     */
    protected function checkSupportedModes(int $mode): void
    {
        if (!in_array($mode, [0, FILE_APPEND])) {
            throw new FilesException($this->getFlLang()->flBadMode($mode));
        }
    }
}
