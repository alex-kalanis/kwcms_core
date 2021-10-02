<?php

namespace KWCMS\modules\Images\Interfaces;


use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Interfaces\IFileEntry;


/**
 * Interface IProcessFiles
 * @package KWCMS\modules\Images\Interfaces
 * Process files in basic ways
 */
interface IProcessFiles
{
    const FREE_NAME_SEPARATOR = '_';

    /**
     * @param IFileEntry $file
     * @param string $description
     * @return bool
     * @throws ImagesException
     */
    public function uploadFile(IFileEntry $file, string $description): bool;

    /**
     * @param string $path
     * @return string
     * @throws ImagesException
     */
    public function readDesc(string $path): string;

    /**
     * @param string $path
     * @param string $content
     * @throws ImagesException
     */
    public function updateDesc(string $path, string $content): void;

    /**
     * @param string $currentPath
     * @param string $toPath full path to target
     * @return bool
     * @throws ImagesException
     */
    public function copyFile(string $currentPath, string $toPath): bool;

    /**
     * @param string $currentPath
     * @param string $toPath full path to target
     * @return bool
     * @throws ImagesException
     */
    public function moveFile(string $currentPath, string $toPath): bool;

    /**
     * @param string $currentPath
     * @param string $toFileName
     * @return bool
     * @throws ImagesException
     */
    public function renameFile(string $currentPath, string $toFileName): bool;

    /**
     * @param string $path
     * @return bool
     * @throws ImagesException
     */
    public function deleteFile(string $path): bool;
}
