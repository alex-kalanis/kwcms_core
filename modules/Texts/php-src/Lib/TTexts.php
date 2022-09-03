<?php

namespace KWCMS\modules\Texts\Lib;


use kalanis\kw_input\Interfaces\IFiltered;
use kalanis\kw_input\Simplified\SessionAdapter;
use kalanis\kw_paths\Extras\UserDir;
use kalanis\kw_paths\Path;
use kalanis\kw_storage\Storage;
use kalanis\kw_tree\TWhereDir;


/**
 * Trait TTexts
 * @package KWCMS\modules\Texts\Lib
 */
trait TTexts
{
    use TWhereDir;

    /** @var UserDir|null */
    protected $userDir = null;
    /** @var Storage|null */
    protected $storage = null;

    protected function initTTexts(Path $path)
    {
        $this->userDir = new UserDir($path);
        Storage\Key\DirKey::setDir($path->getDocumentRoot() . $path->getPathToSystemRoot() . DIRECTORY_SEPARATOR);
        $this->storage = new Storage(new Storage\Factory(new Storage\Key\Factory(), new Storage\Target\Factory()));
        $this->storage->init('volume');
    }

    protected function runTTexts(IFiltered $inputs, string $userDir): void
    {
        $this->initWhereDir(new SessionAdapter(), $inputs);
        $this->userDir->setUserPath($userDir);
        $this->userDir->process();
    }
}
