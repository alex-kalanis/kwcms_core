<?php

namespace kalanis\kw_images\Content;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_images\Sources;
use kalanis\kw_images\TLang;


/**
 * Class Dirs
 * Create specific content
 * @package kalanis\kw_images\Content
 */
class Dirs
{
    use TLang;

    /** @var ImageSize */
    protected $libProcessor = null;
    /** @var Sources\Thumb */
    protected $libThumb = null;
    /** @var Sources\DirDesc */
    protected $libDirDesc = null;
    /** @var Sources\DirThumb */
    protected $libDirThumb = null;

    public function __construct(ImageSize $processor, Sources\Thumb $thumb, Sources\DirDesc $dirDesc, Sources\DirThumb $dirThumb, ?IIMTranslations $lang = null)
    {
        $this->setLang($lang);
        $this->libThumb = $thumb;
        $this->libDirDesc = $dirDesc;
        $this->libDirThumb = $dirThumb;
        $this->libProcessor = $processor;
    }

    /**
     * @param string[] $path
     * @param string $description
     * @throws FilesException
     * @return bool
     */
    public function description(array $path, string $description = ''): bool
    {
        if (!empty($description)) {
            return $this->libDirDesc->set($path, $description);
        } else {
            return $this->libDirDesc->remove($path);
        }
    }

    /**
     * @param string[] $path
     * @param string $fromWhichFile
     * @throws FilesException
     * @throws ImagesException
     * @return bool
     */
    public function updateThumb(array $path, string $fromWhichFile): bool
    {
        return $this->libProcessor->process(
            $this->libThumb->getPath(array_merge($path, [$fromWhichFile])),
            $this->libDirThumb->getPath($path)
        );
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @return bool
     */
    public function removeThumb(array $path): bool
    {
        if ($this->libDirThumb->isHere($path)) {
            if (!$this->libDirThumb->delete($path)) {
                throw new FilesException($this->getLang()->imDirThumbCannotRemoveCurrent());
            }
        }
        return true;
    }
}
