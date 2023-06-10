<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stored;
use kalanis\kw_paths\Stuff;
use kalanis\kw_user_paths\UserDir;


/**
 * Trait TLibAction
 * @package KWCMS\modules\Files\Lib
 * How process actions over content
 */
trait TLibAction
{
    /**
     * @throws FilesException
     * @throws PathsException
     * @return Processor
     */
    protected function getLibAction(): Processor
    {
        $userDir = new UserDir();
        $userDir->setUserPath($this->getUserDir());
        $composite = (new Factory())->getClass(Stored::getPath()->getDocumentRoot() . Stored::getPath()->getPathToSystemRoot());
        return new Processor($composite, $userDir->process()->getFullPath()->getArray(), Stuff::linkToArray($this->getWhereDir()));
    }

    abstract protected function getUserDir(): string;

    abstract protected function getWhereDir(): string;
}
