<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\FilesHelper;
use kalanis\kw_images\ImagesException;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Core\Libs\ImagesTranslations;
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
        $imLang = new ImagesTranslations();
        $flLang = new FilesTranslations();
        return new ProcessFile(
            FilesHelper::getOperations($webRootDir, [], $imLang, $flLang),
            FilesHelper::getUpload($webRootDir, [], $imLang, $flLang),
            FilesHelper::getImages($webRootDir, [], $imLang, $flLang),
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
        $imLang = new ImagesTranslations();
        $flLang = new FilesTranslations();
        return new ProcessDir(
            FilesHelper::getDirs($webRootDir, [], $imLang, $flLang),
            $userPath,
            $currentPath
        );
    }
}
