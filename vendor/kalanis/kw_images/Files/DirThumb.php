<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_paths\Extras\ExtendDir;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_images\TLang;
use kalanis\kw_paths\Stuff;


/**
 * Class DirThumb
 * Directory thumbnail
 * @package kalanis\kw_images\Files
 */
class DirThumb extends AFiles
{
    use TLang;

    const FILE_TEMP = '.tmp';

    protected $thumbExt = '.png';
    protected $libGraphics = null;

    public function __construct(ExtendDir $libExtendDir, Graphics $libGraphics, array $params = [], ?IIMTranslations $lang = null)
    {
        parent::__construct($libExtendDir, $lang);
        $this->libGraphics = $libGraphics;
        $this->thumbExt = !empty($params["tmb_ext"]) ? strval($params["tmb_ext"]) : $this->thumbExt;
    }

    /**
     * @param string $path
     * @throws ImagesException
     */
    public function create(string $path): void
    {
        $thumb = $this->libExtendDir->getWebRootDir() . $this->getPath(Stuff::directory($path));
        $tempThumb = $thumb . static::FILE_TEMP;
        if (is_file($thumb)) {
            if (!rename($thumb, $tempThumb)) {
                throw new ImagesException($this->getLang()->imDirThumbCannotRemoveCurrent());
            }
        }
        try {
            $this->libGraphics->load($this->libExtendDir->getWebRootDir() . $path);
            $this->libGraphics->save($thumb);
        } catch (ImagesException $ex) {
            if (is_file($tempThumb) && !rename($tempThumb, $thumb)) {
                throw new ImagesException($this->getLang()->imDirThumbCannotRestore());
            }
            throw $ex;
        }
        if (is_file($tempThumb) && !unlink($tempThumb)) {
            throw new ImagesException($this->getLang()->imDirThumbCannotRemoveOld());
        }
    }

    /**
     * @param string $whichDir
     * @throws ImagesException
     */
    public function delete(string $whichDir): void
    {
        $whatPath = $this->libExtendDir->getWebRootDir() . $this->getPath($whichDir);
        $this->dataRemove($whatPath, $this->getLang()->imDirThumbCannotRemove());
    }

    public function canUse(string $path): bool
    {
        $thumbDir = $this->libExtendDir->getWebRootDir() . Stuff::removeEndingSlash($path) . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir();
        return is_dir($thumbDir) && is_readable($thumbDir) && is_writable($thumbDir);
    }

    public function getPath(string $path): string
    {
        return Stuff::removeEndingSlash($path) . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir() . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescFile() . $this->thumbExt;
    }
}
