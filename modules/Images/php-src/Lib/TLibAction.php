<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\FilesHelper;
use kalanis\kw_images\ImagesException;
use kalanis\kw_paths\PathsException;
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
     * @param mixed $filesParams
     * @param string[] $userPath
     * @param string[] $currentPath
     * @throws FilesException
     * @throws ImagesException
     * @throws PathsException
     * @return Interfaces\IProcessFiles
     */
    protected function getLibFileAction($filesParams, array $userPath, array $currentPath): Interfaces\IProcessFiles
    {
        $imLang = new ImagesTranslations();
        $flLang = new FilesTranslations();
        return new ProcessFile(
            FilesHelper::getOperations($filesParams, [], $imLang, $flLang),
            FilesHelper::getUpload($filesParams, [], $imLang, $flLang),
            FilesHelper::getImages($filesParams, [], $imLang, $flLang),
            $userPath,
            $currentPath
        );
    }

    /**
     * @param mixed $filesParams
     * @param string[] $userPath
     * @param string[] $currentPath
     * @throws FilesException
     * @throws ImagesException
     * @throws PathsException
     * @return Interfaces\IProcessDirs
     */
    protected function getLibDirAction($filesParams, array $userPath, array $currentPath): Interfaces\IProcessDirs
    {
        $imLang = new ImagesTranslations();
        $flLang = new FilesTranslations();
        return new ProcessDir(
            FilesHelper::getDirs($filesParams, [], $imLang, $flLang),
            $userPath,
            $currentPath
        );
    }
}
