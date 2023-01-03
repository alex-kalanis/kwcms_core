<?php

namespace KWCMS\modules\Images\Interfaces;


use kalanis\kw_files\FilesException;
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
     * @param string $name
     * @throws FilesException
     * @return string
     */
    public function findFreeName(string $name): string;

    /**
     * @param IFileEntry $file
     * @param string $targetName
     * @param string $description
     * @throws FilesException
     * @throws ImagesException
     * @return bool
     */
    public function uploadFile(IFileEntry $file, string $targetName, string $description): bool;

    /**
     * @param string $path
     * @param string $format
     * @throws FilesException
     * @return string
     */
    public function readCreated(string $path, string $format = 'Y-m-d H:i:s'): string;

    /**
     * @param string $path
     * @throws FilesException
     * @return string
     */
    public function readDesc(string $path): string;

    /**
     * @param string $path
     * @param string $content
     * @throws FilesException
     * @return bool
     */
    public function updateDesc(string $path, string $content): bool;

    /**
     * @param string $path
     * @throws FilesException
     * @throws ImagesException
     * @return bool
     */
    public function updateThumb(string $path): bool;

    /**
     * @param string $currentPath
     * @param string $toPath full path to target
     * @throws FilesException
     * @return bool
     */
    public function copyFile(string $currentPath, string $toPath): bool;

    /**
     * @param string $currentPath
     * @param string $toPath full path to target
     * @throws FilesException
     * @return bool
     */
    public function moveFile(string $currentPath, string $toPath): bool;

    /**
     * @param string $currentPath
     * @param string $toFileName
     * @throws FilesException
     * @return bool
     */
    public function renameFile(string $currentPath, string $toFileName): bool;

    /**
     * @param string $path
     * @throws FilesException
     * @return bool
     */
    public function deleteFile(string $path): bool;

    /**
     * @param string $path
     * @throws FilesException
     * @return string
     */
    public function reverseImage(string $path): string;

    /**
     * @param string $path
     * @throws FilesException
     * @return string
     */
    public function reverseThumb(string $path): string;
}
