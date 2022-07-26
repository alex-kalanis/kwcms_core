<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_files\Interfaces\ITypes;
use kalanis\kw_files\Node;
use kalanis\kw_files\Processing\Volume\ProcessDir;
use kalanis\kw_files\Processing\Volume\ProcessFile;
use kalanis\kw_paths\Extras\UserDir;
use kalanis\kw_paths\Stored;
use kalanis\kw_paths\Stuff;


/**
 * Trait TLibAction
 * @package KWCMS\modules\Files\Lib
 * How process actions over content
 */
trait TLibAction
{
    protected function getLibAction(): Processor
    {
        $userDir = $this->getUserDirLib();
        $node = new Node();
        $node->setData(Stuff::pathToArray(Stuff::removeEndingSlash($this->getWhereDir())), 0, ITypes::TYPE_DIR);
        return new Processor(
            new ProcessFile($userDir->getWebRootDir() . $userDir->getHomeDir()),
            new ProcessDir($userDir->getWebRootDir() . $userDir->getHomeDir()),
            $node
        );
    }

    protected function getUserDirLib(): UserDir
    {
        $userDir = new UserDir(Stored::getPath());
        $userDir->setUserPath($this->getUserDir());
        $userDir->process();
        return $userDir;
    }

    abstract protected function getUserDir(): string;

    abstract protected function getWhereDir(): string;
}
