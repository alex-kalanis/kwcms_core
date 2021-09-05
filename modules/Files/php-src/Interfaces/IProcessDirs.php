<?php

namespace KWCMS\modules\Files\Interfaces;


use KWCMS\modules\Files\FilesException;


/**
 * Interface IProcessDirs
 * @package KWCMS\modules\Files\Lib
 * Process dirs in basic ways
 */
interface IProcessDirs
{
    /**
     * @param string $entry
     * @return bool
     * @throws FilesException
     */
    public function createDir(string $entry): bool;

    /**
     * @param string $entry
     * @param string $to
     * @return bool
     * @throws FilesException
     */
    public function copyDir(string $entry, string $to): bool;

    /**
     * @param string $entry
     * @param string $to
     * @return bool
     * @throws FilesException
     */
    public function moveDir(string $entry, string $to): bool;

    /**
     * @param string $entry
     * @param string $to
     * @return bool
     * @throws FilesException
     */
    public function renameDir(string $entry, string $to): bool;

    /**
     * @param string $entry
     * @return bool
     * @throws FilesException
     */
    public function deleteDir(string $entry): bool;
}
