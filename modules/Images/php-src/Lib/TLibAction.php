<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\FilesHelper;
use kalanis\kw_images\ImagesException;
use kalanis\kw_paths\Extras\UserDir;
use kalanis\kw_paths\Stored;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Images\Interfaces;


/**
 * Trait TLibAction
 * @package KWCMS\modules\Images\Lib
 * How process actions over content
 */
trait TLibAction
{
    /**
     * @throws FilesException
     * @throws ImagesException
     * @return Interfaces\IProcessFiles
     */
    protected function getLibFileAction(): Interfaces\IProcessFiles
    {
        $userDir = new UserDir(Stored::getPath());
        $userDir->setUserPath($this->getUserDir());
        $userDir->process();
        $webRootDir = $userDir->getWebRootDir() . $userDir->getHomeDir();
        return new ProcessFile(
            FilesHelper::getOperations($webRootDir),
            FilesHelper::getUpload($webRootDir),
            FilesHelper::getImages($webRootDir),
            Stuff::sanitize($this->getWhereDir()) . DIRECTORY_SEPARATOR
        );
    }

    /**
     * @throws FilesException
     * @throws ImagesException
     * @return Interfaces\IProcessDirs
     */
    protected function getLibDirAction(): Interfaces\IProcessDirs
    {
        $userDir = new UserDir(Stored::getPath());
        $userDir->setUserPath($this->getUserDir());
        $userDir->process();
        return new ProcessDir(
            FilesHelper::getDirs($userDir->getWebRootDir() . $userDir->getHomeDir()),
            Stuff::sanitize($this->getWhereDir()) . DIRECTORY_SEPARATOR
        );
    }

    abstract protected function getUserDir(): string;

    abstract protected function getWhereDir(): string;
}
