<?php

namespace kalanis\kw_images\Content;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\Configs;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\ISizes;
use kalanis\kw_images\Sources;
use kalanis\kw_mime\MimeException;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;


/**
 * Class ImageUpload
 * Process uploaded content
 * @package kalanis\kw_images\Content
 */
class ImageUpload
{
    protected Graphics $graphics;
    protected Sources\Image $imageSource;
    protected ISizes $config;
    protected Images $images;
    protected Configs\ProcessorConfig $procConfig;
    protected ImageOrientate $orientate;
    protected ImageSize $libSizes;

    public function __construct(
        Graphics $graphics,
        Sources\Image $libImage,
        ISizes $config,
        Images $images,
        Configs\ProcessorConfig $procConfig,
        ImageOrientate $orientate,
        ImageSize $libSizes
    )
    {
        $this->graphics = $graphics;
        $this->imageSource = $libImage;
        $this->config = $config;
        $this->images = $images;
        $this->procConfig = $procConfig;
        $this->orientate = $orientate;
        $this->libSizes = $libSizes;
    }

    /**
     * @param string[] $wantedPath where we want to store the file
     * @param string $name
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    public function findFreeName(array $wantedPath, string $name): string
    {
        $name = Stuff::canonize($name);
        $ext = $this->procConfig->canLimitExt() && !empty($this->procConfig->getDefaultExt())
            ? $this->procConfig->getDefaultExt()
            : Stuff::fileExt($name);
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
     * @param bool $orientate
     * @throws FilesException
     * @throws ImagesException
     * @throws MimeException
     * @throws PathsException
     * @return bool
     */
    public function process(array $wantedPath, string $tempPath = '', string $description = '', bool $orientate = false): bool
    {
        $fullPath = array_values($wantedPath);
        // check file
        $this->graphics->setSizes($this->config)->check($tempPath, $fullPath);

        // store image
        $uploaded = strval(@file_get_contents($tempPath));
        $this->imageSource->set($fullPath, $uploaded);
        @unlink($tempPath);

        // orientate if set
        if ($orientate) {
            try {
                $this->orientate->process($fullPath);
            } catch (ImagesException $ex) {
                // this failure will be skipped
            }
        }

        // resize if set
        if ($this->procConfig->canLimitSize() && !empty($this->config->getMaxInWidth()) && !empty($this->config->getMaxInHeight())) {
            $this->libSizes->process($fullPath, $fullPath);
        }

        // thumbs
        $this->images->removeThumb($fullPath);
        if ($this->procConfig->hasThumb()) {
            $this->images->updateThumb($fullPath);
        }

        // description
        $this->images->updateDescription($fullPath, $description);
        return true;
    }
}
