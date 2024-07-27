<?php

namespace KWCMS\modules\Images\Interfaces;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\Content\Images;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Interfaces\IFileEntry;
use kalanis\kw_mime\MimeException;
use kalanis\kw_paths\PathsException;


/**
 * Interface IProcessFiles
 * @package KWCMS\modules\Images\Interfaces
 * Process files in basic ways
 */
interface IProcessFiles
{
    /**
     * @param string $name provided current name
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    public function findFreeName(string $name): string;

    /**
     * @param IFileEntry $file File which came from the client
     * @param string $targetName Which name has been used for file
     * @param string $description Description of image, empty will remove existing
     * @param bool $orientate try to rotate image into its real position
     * @throws FilesException
     * @throws ImagesException
     * @throws MimeException
     * @throws PathsException
     * @return bool
     */
    public function uploadFile(IFileEntry $file, string $targetName, string $description, bool $orientate): bool;

    /**
     * @param string $path
     * @param string $format
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    public function readCreated(string $path, string $format = 'Y-m-d H:i:s'): string;

    /**
     * @param string $path
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    public function readDesc(string $path): string;

    /**
     * @param string $path
     * @param string $content
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function updateDesc(string $path, string $content): bool;

    /**
     * @param string $path
     * @throws FilesException
     * @throws ImagesException
     * @throws MimeException
     * @throws PathsException
     * @return bool
     */
    public function updateThumb(string $path): bool;

    /**
     * @param string $currentPath
     * @param string $toPath full path to target
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function copyFile(string $currentPath, string $toPath): bool;

    /**
     * @param string $currentPath
     * @param string $toPath full path to target
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function moveFile(string $currentPath, string $toPath): bool;

    /**
     * @param string $currentPath
     * @param string $toFileName
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function renameFile(string $currentPath, string $toFileName): bool;

    /**
     * @param string $path
     * @throws FilesException
     * @throws PathsException
     * @return bool
     */
    public function deleteFile(string $path): bool;

    /**
     * @param string $path
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    public function reverseImage(string $path): string;

    /**
     * @param string $path
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    public function reverseThumb(string $path): string;

    public function getLibImage(): Images;
}
