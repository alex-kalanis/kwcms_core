<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_images\Files;
use kalanis\kw_images\ImagesException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_paths\PathsException;
use KWCMS\modules\Images\Interfaces\IProcessDirs;


/**
 * Class ProcessDir
 * @package KWCMS\modules\Images\Lib
 * Process dirs which represent galleries
 * @see \KWCMS\modules\Files\Lib\Processor
 * @todo: use KW_FILES as data source - that will remove that part with volume service
 */
class ProcessDir implements IProcessDirs
{
    protected $libFiles = null;
    protected $sourcePath = '';

    public function __construct(Files $libFiles, string $sourcePath)
    {
        $this->libFiles = $libFiles;
        $this->sourcePath = $sourcePath;
    }

    public function canUse(): bool
    {
        return $this->libFiles->getLibDirDesc()->canUse($this->sourcePath);
    }

    public function createDir(string $target, string $name): bool
    {
        try {
            $targetPath = Stuff::removeEndingSlash($target) . DIRECTORY_SEPARATOR;
            return $this->libFiles->getLibDirDesc()->getProcessor()->createDir($targetPath, $name, true);
        } catch (PathsException $ex) {
            throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function createExtra(): bool
    {
        try {
            return $this->libFiles->getLibDirDesc()->getProcessor()->makeExtended($this->sourcePath);
        } catch (PathsException $ex) {
            throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function getDesc(): string
    {
        return $this->libFiles->getLibDirDesc()->get($this->sourcePath);
    }

    public function updateDesc(string $content): bool
    {
        return empty($content)
            ? $this->libFiles->getLibDirDesc()->remove($this->sourcePath)
            : $this->libFiles->getLibDirDesc()->set($this->sourcePath, $content)
        ;
    }

    public function getThumb(): string
    {
        return $this->libFiles->getLibDirThumb()->getPath($this->sourcePath);
    }

    public function updateThumb(string $filePath): bool
    {
        $this->libFiles->getLibDirThumb()->create(Stuff::sanitize($filePath));
        return true;
    }
}
