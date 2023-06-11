<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_images\Content;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Images\Interfaces\IProcessDirs;


/**
 * Class ProcessDir
 * @package KWCMS\modules\Images\Lib
 * Process dirs which represent galleries
 * @see \KWCMS\modules\Files\Lib\Processor
 */
class ProcessDir implements IProcessDirs
{
    /** @var Content\Dirs|null */
    protected $libDirs = null;
    /** @var string[] */
    protected $userDir = [];
    /** @var string[] */
    protected $currentDir = [];

    public function __construct(Content\Dirs $dirs, array $userDir, array $currentDir)
    {
        $this->libDirs = $dirs;
        $this->userDir = $userDir;
        $this->currentDir = $currentDir;
    }

    public function canUse(): bool
    {
        return $this->libDirs->canUse(array_merge($this->userDir, $this->currentDir));
    }

    public function createDir(string $target, string $name): bool
    {
        return $this->libDirs->create(array_merge($this->userDir, Stuff::linkToArray($target), [Stuff::canonize($name)]));
    }

    public function createExtra(): bool
    {
        return $this->libDirs->createExtra(array_merge($this->userDir, $this->currentDir));
    }

    public function getDesc(): string
    {
        return $this->libDirs->getDescription(array_merge($this->userDir, $this->currentDir));
    }

    public function updateDesc(string $content): bool
    {
        return $this->libDirs->updateDescription(array_merge($this->userDir, $this->currentDir), $content);
    }

    public function getThumb()
    {
        return $this->libDirs->getThumb(array_merge($this->userDir, $this->currentDir));
    }

    public function updateThumb(string $filePath): bool
    {
        return $this->libDirs->updateThumb(array_merge($this->userDir, $this->currentDir), Stuff::sanitize($filePath));
    }

    public function removeThumb(): bool
    {
        return $this->libDirs->removeThumb(array_merge($this->userDir, $this->currentDir));
    }
}
