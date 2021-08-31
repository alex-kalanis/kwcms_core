<?php

namespace KWCMS\modules\Files\Interfaces;


use KWCMS\modules\Files\FilesException;
use KWCMS\modules\Files\Lib\FileForm;


/**
 * Interface IProcessFiles
 * @package KWCMS\modules\Files\Lib
 * Process files in many ways
 */
interface IProcessFiles
{
    const FREE_NAME_SEPARATOR = '_';

    /**
     * @param FileForm $form
     * @return bool
     * @throws FilesException
     */
    public function uploadFile(FileForm $form): bool;

    /**
     * @param FileForm $form
     * @return bool
     * @throws FilesException
     */
    public function copyFile(FileForm $form): bool;

    /**
     * @param FileForm $form
     * @return bool
     * @throws FilesException
     */
    public function moveFile(FileForm $form): bool;

    /**
     * @param FileForm $form
     * @return bool
     * @throws FilesException
     */
    public function renameFile(FileForm $form): bool;

    /**
     * @param FileForm $form
     * @return bool
     * @throws FilesException
     */
    public function deleteFile(FileForm $form): bool;
}
