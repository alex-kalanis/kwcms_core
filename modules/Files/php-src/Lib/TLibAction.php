<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_confs\Config;
use kalanis\kw_extras\UserDir;
use KWCMS\modules\Files\Interfaces;


/**
 * Trait TLibAction
 * @package KWCMS\modules\Files\Lib
 * How process actions over content
 */
trait TLibAction
{
    protected function getLibFileAction(): Interfaces\IProcessFiles
    {
        $userDir = new UserDir(Config::getPath());
        $userDir->setUserPath($this->getUserDir());
        $userDir->process();
        return new ProcessFile(
            $userDir->getWebRootDir() . $userDir->getRealDir(), $this->getWhereDir()
        );
    }

    protected function getLibDirAction(): Interfaces\IProcessDirs
    {
        $userDir = new UserDir(Config::getPath());
        $userDir->setUserPath($this->getUserDir());
        $userDir->process();
        return new ProcessDir(
            $userDir->getWebRootDir() . $userDir->getRealDir(), $this->getWhereDir()
        );
    }

    abstract protected function getUserDir(): string;

    abstract protected function getWhereDir(): string;
}
