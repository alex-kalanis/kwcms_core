<?php

namespace KWCMS\modules\Texts\Lib;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_input\Interfaces\IFiltered;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_paths\Path;
use kalanis\kw_paths\PathsException;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Core\Libs\FilesTranslations;


/**
 * Trait TTexts
 * @package KWCMS\modules\Texts\Lib
 */
trait TTexts
{
    use TWhereDir;

    /** @var UserDir */
    protected $userDir = null;
    /** @var CompositeAdapter */
    protected $files = null;

    /**
     * @param Path $path
     * @throws FilesException
     * @throws PathsException
     */
    protected function initTTexts(Path $path)
    {
        $this->userDir = new UserDir(new Translations());
        $this->files = (new Factory(new FilesTranslations()))->getClass(
            $path->getDocumentRoot() . $path->getPathToSystemRoot() . DIRECTORY_SEPARATOR
        );
    }

    protected function runTTexts(IFiltered $inputs, string $userDir): void
    {
        $this->initWhereDir(new SessionAdapter(), $inputs);
        $this->userDir->setUserPath($userDir);
    }
}
