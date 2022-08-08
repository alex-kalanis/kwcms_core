<?php

namespace kalanis\kw_images\Content;


use kalanis\kw_files\Extended\Config;
use kalanis\kw_files\FilesException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_images\Sources;
use kalanis\kw_images\TLang;
use kalanis\kw_paths\Stuff;


/**
 * Class Dirs
 * Create specific content
 * @package kalanis\kw_images\Content
 */
class Dirs
{
    use TLang;

    /** @var Create */
    protected $opCreate = null;
    /** @var Sources\Thumb */
    protected $libThumb = null;
    /** @var Sources\DirDesc */
    protected $libDirDesc = null;
    /** @var Sources\DirThumb */
    protected $libDirThumb = null;
    /** @var Config */
    protected $extendConfig = null;

    public function __construct(Create $opCreate, Config $extendConfig, Sources\Thumb $thumb, Sources\DirDesc $dirDesc, Sources\DirThumb $dirThumb, ?IIMTranslations $lang = null)
    {
        $this->setLang($lang);
        $this->opCreate = $opCreate;
        $this->libThumb = $thumb;
        $this->libDirDesc = $dirDesc;
        $this->libDirThumb = $dirThumb;
        $this->extendConfig = $extendConfig;
    }

    /**
     * @param string $path
     * @param string $description
     * @throws FilesException
     */
    public function description(string $path, string $description = ''): void
    {
        if (!empty($description)) {
            $this->libDirDesc->set(Stuff::pathToArray($path), $description);
        } else {
            $this->libDirDesc->remove(Stuff::pathToArray($path));
        }
    }

    /**
     * @param string $path
     * @throws FilesException
     * @throws ImagesException
     */
    public function thumb(string $path): void
    {
        $dir = Stuff::directory($path);
        $file = Stuff::filename($path);
        $tempFile = $this->extendConfig->getDescFile() . $this->extendConfig->getThumbExt();
        $backupFile = $this->extendConfig->getDescFile() . $this->extendConfig->getThumbExt() . $this->extendConfig->getThumbTemp();
        $backupPath = $dir . DIRECTORY_SEPARATOR . $backupFile;

        if ($this->libDirThumb->isHere($path)) {
            if (!$this->libThumb->rename($path, $tempFile, $backupFile)) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getLang()->imDirThumbCannotRemoveCurrent());
            }
            // @codeCoverageIgnoreEnd
        }
        try {
            if (!$this->libThumb->isHere($path)) {
                $this->opCreate->thumb($path);
                $this->libDirThumb->set($path, $this->libThumb->get($path));
                $this->libThumb->delete($dir, $file);
            } else {
                $this->libDirThumb->set($path, $this->libThumb->get($path));
            }
        } catch (FilesException $ex) {
            if ($this->libThumb->isHere($path) && !$this->libThumb->rename($dir, $backupFile, $tempFile)) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getLang()->imDirThumbCannotRestore());
            }
            // @codeCoverageIgnoreEnd
            throw $ex;
        }
        if ($this->libThumb->isHere($backupPath) && !$this->libThumb->delete($dir, $backupFile)) {
            // @codeCoverageIgnoreStart
            throw new FilesException($this->getLang()->imDirThumbCannotRemoveOld());
        }
        // @codeCoverageIgnoreEnd
    }
}
