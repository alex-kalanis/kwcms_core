<?php

namespace kalanis\kw_images\Content;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Sources;


/**
 * Class Images
 * Process images parts
 * @package kalanis\kw_images\Content
 */
class Images
{
    /** @var ImageSize */
    protected $processor = null;
    /** @var Sources\Thumb */
    protected $libThumb = null;
    /** @var Sources\Desc */
    protected $libDesc = null;

    public function __construct(ImageSize $processor, Sources\Thumb $thumb, Sources\Desc $desc)
    {
        $this->processor = $processor;
        $this->libThumb = $thumb;
        $this->libDesc = $desc;
    }

    /**
     * @param string[] $wantedPath where we want to store the file
     * @throws FilesException
     * @throws ImagesException
     * @return bool
     */
    public function updateThumb(array $wantedPath): bool
    {
        return $this->processor->process(
            $this->processor->getImage()->getPath($wantedPath),
            $this->libThumb->getPath($wantedPath)
        );
    }

    /**
     * @param string[] $wantedPath where we want to store the file
     * @throws FilesException
     * @return bool
     */
    public function removeThumb(array $wantedPath): bool
    {
        $fileName = strval(array_pop($wantedPath));
        return $this->libThumb->delete($wantedPath, $fileName);
    }

    /**
     * @param string[] $wantedPath where we want to store the file
     * @param string $description
     * @throws FilesException
     * @return bool
     */
    public function updateDescription(array $wantedPath, string $description = ''): bool
    {
        if (!empty($description)) {
            return $this->libDesc->set($wantedPath, $description);
        } else {
            $fileName = strval(array_pop($wantedPath));
            return $this->libDesc->delete($wantedPath, $fileName);
        }
    }
}
