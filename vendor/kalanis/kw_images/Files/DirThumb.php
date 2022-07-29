<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_files\Extended\Processor;
use kalanis\kw_files\FilesException;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_paths\Stuff;


/**
 * Class DirThumb
 * Directory thumbnail
 * @package kalanis\kw_images\Files
 */
class DirThumb extends AFiles
{
    const FILE_TEMP = '.tmp';

    /** @var Graphics\DirConfig */
    protected $libConfig = '.png';

    public function __construct(Processor $libProcessor, Graphics\DirConfig $config, ?IIMTranslations $lang = null)
    {
        parent::__construct($libProcessor, $lang);
        $this->libConfig = $config;
    }

    /**
     * @param string $path
     * @throws FilesException
     */
    public function create(string $path): void
    {
        $dir = Stuff::directory($path);
        $file = Stuff::pathToArray($path);
        $thumb = $this->getPath($dir);
        $tempThumb = $this->getPath($dir . static::FILE_TEMP);
        if ($this->libProcessor->getNodeProcessor()->isFile($thumb)) {
            if (!$this->libProcessor->getFileProcessor()->moveFile($thumb, $tempThumb)) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getLang()->imDirThumbCannotRemoveCurrent());
            }
            // @codeCoverageIgnoreEnd
        }
        try {
            if ($this->libProcessor->getNodeProcessor()->exists($thumb)) {
                $this->libProcessor->getFileProcessor()->deleteFile($thumb);
            }
            $this->libProcessor->getFileProcessor()->copyFile($file, $thumb);
        } catch (FilesException $ex) {
            if ($this->libProcessor->getNodeProcessor()->isFile($tempThumb)
                && !$this->libProcessor->getFileProcessor()->moveFile($tempThumb, $thumb)
            ) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getLang()->imDirThumbCannotRestore());
            }
            // @codeCoverageIgnoreEnd
            throw $ex;
        }
        if ($this->libProcessor->getNodeProcessor()->isFile($tempThumb)
            && !$this->libProcessor->getFileProcessor()->deleteFile($tempThumb)
        ) {
            // @codeCoverageIgnoreStart
            throw new FilesException($this->getLang()->imDirThumbCannotRemoveOld());
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $whichDir
     * @throws FilesException
     */
    public function delete(string $whichDir): void
    {
        $whatPath = $this->getPath($whichDir);
        $this->dataRemove($whatPath, $this->getLang()->imDirThumbCannotRemove());
    }

    public function getPath(string $path): array
    {
        return Stuff::pathToArray(Stuff::removeEndingSlash($path)) + [
            $this->libProcessor->getConfig()->getThumbDir(),
            $this->libProcessor->getConfig()->getDescFile() . $this->libConfig->getThumbExt()
        ];
    }
}
