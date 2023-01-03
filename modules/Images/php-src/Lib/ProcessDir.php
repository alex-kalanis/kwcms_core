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
    /** @var string */
    protected $sourcePath = '';

    public function __construct(Content\Dirs $dirs, string $sourcePath)
    {
        $this->libDirs = $dirs;
        $this->sourcePath = $sourcePath;
    }

    public function canUse(): bool
    {
        return $this->libDirs->canUse(Stuff::linkToArray($this->sourcePath));
    }

    public function createDir(string $target, string $name): bool
    {
        $path = array_merge(Stuff::linkToArray($target), [Stuff::canonize($name)]);
        return $this->libDirs->create($path);
    }

    public function createExtra(): bool
    {
        return $this->libDirs->createExtra(Stuff::linkToArray($this->sourcePath));
    }

    public function getDesc(): string
    {
        return $this->libDirs->getDescription(Stuff::linkToArray($this->sourcePath));
    }

    public function updateDesc(string $content): bool
    {
        return $this->libDirs->updateDescription(Stuff::linkToArray($this->sourcePath), $content);
    }

    public function getThumb()
    {
        return $this->libDirs->getThumb(Stuff::linkToArray($this->sourcePath));
    }

    public function updateThumb(string $filePath): bool
    {
        return $this->libDirs->updateThumb(Stuff::linkToArray($this->sourcePath), Stuff::sanitize($filePath));
    }

    public function removeThumb(): bool
    {
        return $this->libDirs->removeThumb(Stuff::linkToArray($this->sourcePath));
    }
}
