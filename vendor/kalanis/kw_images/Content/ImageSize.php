<?php

namespace kalanis\kw_images\Content;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_images\Interfaces\ISizes;
use kalanis\kw_images\Sources;
use kalanis\kw_images\Traits\TLang;
use kalanis\kw_mime\MimeException;
use kalanis\kw_paths\PathsException;


/**
 * Class ImageSize
 * Process image from source
 * @package kalanis\kw_images\Content
 */
class ImageSize
{
    use TLang;

    protected Sources\Image $libImage;
    protected Graphics $libGraphics;
    protected ISizes $config;

    public function __construct(Graphics $graphics, ISizes $config, Sources\Image $image, ?IIMTranslations $lang = null)
    {
        $this->setImLang($lang);
        $this->libImage = $image;
        $this->libGraphics = $graphics;
        $this->config = $config;
    }

    /**
     * @param string[] $sourcePath
     * @param string[] $targetPath
     * @throws FilesException
     * @throws ImagesException
     * @throws MimeException
     * @throws PathsException
     * @return bool
     */
    public function process(array $sourcePath, array $targetPath): bool
    {
        $sourceFull = array_values($sourcePath);
        $targetFull = array_values($targetPath);

        $tempPath = strval(tempnam(sys_get_temp_dir(), $this->config->getTempPrefix()));

        // get from the storage
        $resource = $this->libImage->get($sourceFull);
        if (empty($resource)) {
            @unlink($tempPath);
            throw new FilesException($this->getImLang()->imThumbCannotGetBaseImage());
        }

        if (false === @file_put_contents($tempPath, $resource)) {
            // @codeCoverageIgnoreStart
            @unlink($tempPath);
            throw new FilesException($this->getImLang()->imThumbCannotStoreTemporaryImage());
        }
        // @codeCoverageIgnoreEnd

        // now process image locally
        try {
            $this->libGraphics->setSizes($this->config)->resize($tempPath, $sourceFull, $targetFull);
        } catch (ImagesException $ex) {
            // clear when fails
            @unlink($tempPath);
            throw $ex;
        }

        // return result to the storage as new file
        $result = @file_get_contents($tempPath);
        if (false === $result) {
            // @codeCoverageIgnoreStart
            @unlink($tempPath);
            throw new FilesException($this->getImLang()->imThumbCannotLoadTemporaryImage());
        }
        // @codeCoverageIgnoreEnd

        $set = $this->libImage->set($targetFull, $result);
        @unlink($tempPath);
        return $set;
    }

    public function getImage(): Sources\Image
    {
        return $this->libImage;
    }
}
