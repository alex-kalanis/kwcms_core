<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_extras\ExtendDir;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_paths\Stuff;


/**
 * Class DirThumb
 * Directory thumbnail
 * @package kalanis\kw_images\Files
 */
class DirThumb extends AFiles
{
    const FILE_TEMP = '.tmp';

    protected $thumbExt = 'png';
    protected $libGraphics = null;

    public function __construct(ExtendDir $libExtendDir, Graphics $libGraphics, array $params = [])
    {
        parent::__construct($libExtendDir);
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
                throw new ImagesException('Cannot remove current thumb!');
            }
        }
        try {
            $this->libGraphics->load($this->libExtendDir->getWebRootDir() . $path);
            $this->libGraphics->save($thumb);
        } catch (ImagesException $ex) {
            if (is_file($tempThumb) && !rename($tempThumb, $thumb)) {
                throw new ImagesException('Cannot remove current thumb back!');
            }
            throw $ex;
        }
        if (is_file($tempThumb) && !unlink($tempThumb)) {
            throw new ImagesException('Cannot remove old thumb!');
        }
    }

    /**
     * @param string $whichDir
     * @throws ImagesException
     */
    public function delete(string $whichDir): void
    {
        $whatPath = $this->libExtendDir->getWebRootDir() . $this->getPath($whichDir);
        $this->dataRemove($whatPath, 'Cannot remove dir thumb!');
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
