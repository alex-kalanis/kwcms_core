<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_extras\ExtendDir;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_paths\Stuff;


/**
 * Class Thumb
 * File thumbnail
 * @package kalanis\kw_images\Files
 */
class Thumb extends AFiles
{
    const FILE_TEMP = '.tmp';

    protected $maxWidth = 180;
    protected $maxHeight = 180;
    protected $libGraphics = null;

    public function __construct(ExtendDir $libExtendDir, Graphics $libGraphics, array $params = [])
    {
        parent::__construct($libExtendDir);
        $this->libGraphics = $libGraphics;
        $this->maxWidth = !empty($params["tmb_width"]) ? strval($params["tmb_width"]) : $this->maxWidth;
        $this->maxHeight = !empty($params["tmb_height"]) ? strval($params["tmb_height"]) : $this->maxHeight;
    }

    /**
     * @param string $path
     * @throws ImagesException
     */
    public function create(string $path): void
    {
        $thumb = $this->getPath($path);
        $tempThumb = $thumb . static::FILE_TEMP;
        if (is_file($thumb)) {
            if (!rename($thumb, $tempThumb)) {
                throw new ImagesException('Cannot remove current thumb!');
            }
        }
        try {
            $this->libGraphics->load($path);
            $sizes = $this->calculateSize($this->libGraphics->width(), $this->maxWidth, $this->libGraphics->height(), $this->maxHeight);
            $this->libGraphics->resample($sizes['width'], $sizes['height']);
            $this->libGraphics->save($thumb);
        } catch (ImagesException $ex) {
            if (!rename($tempThumb, $thumb)) {
                throw new ImagesException('Cannot remove current thumb back!');
            }
            throw $ex;
        }
        if (is_file($tempThumb) && !unlink($tempThumb)) {
            throw new ImagesException('Cannot remove old thumb!');
        }
    }

    /**
     * @param string $path
     * @throws ImagesException
     */
    public function remove(string $path): void
    {
        $this->deleteFile($this->getPath($path), 'Cannot remove thumb!');
    }

    public function getPath(string $path): string
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);
        return $filePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir() . DIRECTORY_SEPARATOR . $fileName;
    }
}
