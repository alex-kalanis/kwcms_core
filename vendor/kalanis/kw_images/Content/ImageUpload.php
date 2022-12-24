<?php

namespace kalanis\kw_images\Content;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\ISizes;
use kalanis\kw_images\Sources;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Stuff;


/**
 * Class ImageUpload
 * Process uploaded content
 * @package kalanis\kw_images\Content
 */
class ImageUpload
{
    /** @var Graphics */
    protected $graphics = null;
    /** @var Sources\Image */
    protected $imageSource = null;
    /** @var ISizes */
    protected $config = null;
    /** @var Images */
    protected $images = null;

    public function __construct(Graphics $graphics, Sources\Image $libImage, ISizes $config, Images $images)
    {
        $this->graphics = $graphics;
        $this->imageSource = $libImage;
        $this->config = $config;
        $this->images = $images;
    }

    /**
     * @param string[] $wantedPath where we want to store the file
     * @param string $name
     * @throws FilesException
     * @return string
     */
    public function findFreeName(array $wantedPath, string $name): string
    {
        $name = Stuff::canonize($name);
        $ext = Stuff::fileExt($name);
        if (0 < mb_strlen($ext)) {
            $ext = IPaths::SPLITTER_DOT . $ext;
        }
        $fileName = Stuff::fileBase($name);
        return $this->imageSource->findFreeName($wantedPath, $fileName, $ext);
    }

    /**
     * @param string[] $wantedPath where we want to store the file
     * @param string $tempPath where the file is accessible after upload
     * @param string $description
     * @param bool $hasThumb
     * @param bool $wantResize
     * @throws FilesException
     * @throws ImagesException
     * @return bool
     */
    public function process(array $wantedPath, string $tempPath = '', string $description = '', bool $hasThumb = true, bool $wantResize = false): bool
    {
        $fullPath = array_values($wantedPath);
        $fileName = strval(array_pop($wantedPath));
        // check file
        $this->graphics->setSizes($this->config)->check($tempPath);

        // resize if set
        if ($wantResize) {
            $this->graphics->setSizes($this->config)->resize($tempPath, $fileName);
        }

        // store image
        $uploaded = strval(@file_get_contents($tempPath));
        $this->imageSource->set($fullPath, $uploaded);

        // thumbs
        $this->images->removeThumb($fullPath);
        if ($hasThumb) {
            $this->images->updateThumb($fullPath);
        }

        // description
        $this->images->updateDescription($fullPath, $description);
        return true;
    }
}
