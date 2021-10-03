<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_images\Files;
use KWCMS\modules\Images\Interfaces\IProcessDirs;


/**
 * Class ProcessDir
 * @package KWCMS\modules\Images\Lib
 * Process dirs which represent galleries
 * @see \KWCMS\modules\Files\Lib\ProcessDir
 * @todo: use KW_STORAGE as data source - that will remove that part with volume service
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

    public function getDesc(string $dirPath): string
    {
        return $this->libFiles->getLibDirDesc()->get($dirPath);
    }

    public function updateDesc(string $dirPath, string $content): void
    {
        if (empty($content)) {
            $this->libFiles->getLibDirDesc()->remove($dirPath);
        } else {
            $this->libFiles->getLibDirDesc()->set($dirPath, $content);
        }
    }

    public function getThumb(string $dirPath): string
    {
        return $this->libFiles->getLibDirThumb()->getPath($dirPath);
    }

    public function updateThumb(string $filePath): bool
    {
        $this->libFiles->getLibDirThumb()->create($filePath);
        return true;
    }
}
