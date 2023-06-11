<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\FilesHelper;
use kalanis\kw_images\ImagesException;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use KWCMS\modules\Images\Interfaces;


/**
 * Trait TLibAction
 * @package KWCMS\modules\Images\Lib
 * How process actions over content
 */
trait TLibAction
{
    /**
     * @param string[] $userPath
     * @param string[] $currentPath
     * @throws FilesException
     * @throws ImagesException
     * @throws PathsException
     * @return Interfaces\IProcessFiles
     */
    protected function getLibFileAction(array $userPath, array $currentPath): Interfaces\IProcessFiles
    {
        $webRootDir = Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot() . DIRECTORY_SEPARATOR;
        return new ProcessFile(
            FilesHelper::getOperations($webRootDir),
            FilesHelper::getUpload($webRootDir),
            FilesHelper::getImages($webRootDir),
            $userPath,
            $currentPath
        );
    }

    /**
     * @param string[] $userPath
     * @param string[] $currentPath
     * @throws FilesException
     * @throws ImagesException
     * @throws PathsException
     * @return Interfaces\IProcessDirs
     */
    protected function getLibDirAction(array $userPath, array $currentPath): Interfaces\IProcessDirs
    {
        $webRootDir = Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot() . DIRECTORY_SEPARATOR;
        return new ProcessDir(
            FilesHelper::getDirs($webRootDir),
            $userPath,
            $currentPath
        );
    }
}
