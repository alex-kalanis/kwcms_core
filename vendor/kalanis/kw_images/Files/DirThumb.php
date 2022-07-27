<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_files\Extended\Processor;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
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

    protected $thumbExt = '.png';
    protected $libGraphics = null;

    public function __construct(Processor $libProcessor, Graphics $libGraphics, array $params = [], ?IIMTranslations $lang = null)
    {
        parent::__construct($libProcessor, $lang);
        $this->libGraphics = $libGraphics;
        $this->thumbExt = !empty($params['tmb_ext']) ? strval($params['tmb_ext']) : $this->thumbExt;
    }

    /**
     * @param string $path
     * @throws ImagesException
     */
    public function create(string $path): void
    {
        $thumb = $this->libProcessor->getWebRootDir() . $this->getPath(Stuff::directory($path));
        $tempThumb = $thumb . static::FILE_TEMP;
        if ($this->libProcessor->isFile($thumb)) {
            if (!rename($thumb, $tempThumb)) {
                // @codeCoverageIgnoreStart
                throw new ImagesException($this->getLang()->imDirThumbCannotRemoveCurrent());
            }
            // @codeCoverageIgnoreEnd
        }
        try {
            $this->libGraphics->load($this->libProcessor->getWebRootDir() . $path);
            $this->libGraphics->save($thumb);
        } catch (ImagesException $ex) {
            if ($this->libProcessor->isFile($tempThumb) && !rename($tempThumb, $thumb)) {
                // @codeCoverageIgnoreStart
                throw new ImagesException($this->getLang()->imDirThumbCannotRestore());
            }
            // @codeCoverageIgnoreEnd
            throw $ex;
        }
        if ($this->libProcessor->isFile($tempThumb) && !unlink($tempThumb)) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getLang()->imDirThumbCannotRemoveOld());
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $whichDir
     * @throws ImagesException
     */
    public function delete(string $whichDir): void
    {
        $whatPath = $this->libProcessor->getWebRootDir() . $this->getPath($whichDir);
        $this->dataRemove($whatPath, $this->getLang()->imDirThumbCannotRemove());
    }

    public function canUse(string $path): bool
    {
        $thumbDir = $this->libProcessor->getWebRootDir() . Stuff::removeEndingSlash($path) . DIRECTORY_SEPARATOR . $this->libProcessor->getThumbDir();
        return $this->libProcessor->isDir($thumbDir) && is_readable($thumbDir) && is_writable($thumbDir);
    }

    public function getPath(string $path): string
    {
        return Stuff::removeEndingSlash($path) . DIRECTORY_SEPARATOR . $this->libProcessor->getThumbDir() . DIRECTORY_SEPARATOR . $this->libProcessor->getDescFile() . $this->thumbExt;
    }
}
