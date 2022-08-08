<?php

namespace kalanis\kw_images\Content;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Sources;
use kalanis\kw_paths\Stuff;


/**
 * Class BasicOperations
 * Operations over files
 * @package kalanis\kw_images\Content
 */
class BasicOperations
{
    /** @var Sources\Image */
    protected $libImage = null;
    /** @var Sources\Thumb */
    protected $libThumb = null;
    /** @var Sources\Desc */
    protected $libDesc = null;

    public function __construct(Sources\Image $image, Sources\Thumb $thumb, Sources\Desc $desc)
    {
        $this->libImage = $image;
        $this->libThumb = $thumb;
        $this->libDesc = $desc;
    }

    /**
     * @param string $currentPath
     * @param string $targetDir
     * @param bool $overwrite
     * @throws ImagesException
     * @throws FilesException
     * @return bool
     */
    public function copy(string $currentPath, string $targetDir, bool $overwrite = false): bool
    {
        $origDir = Stuff::removeEndingSlash(Stuff::directory($currentPath));
        $fileName = Stuff::filename($currentPath);
        $targetDir = Stuff::removeEndingSlash($targetDir);
        try {
            $this->libImage->copy($fileName, $origDir, $targetDir, $overwrite);
        } catch (FilesException $ex) {
            throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
        }
        if ($this->libThumb->isHere($currentPath)) {
            try {
                $this->libThumb->copy($fileName, $origDir, $targetDir, $overwrite);
            } catch (FilesException $ex) {
                $this->libImage->delete($targetDir, $fileName);
                throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
        if ($this->libDesc->isHere($currentPath)) {
            try {
                $this->libDesc->copy($fileName, $origDir, $targetDir, $overwrite);
            } catch (FilesException $ex) {
                $this->libThumb->delete($targetDir, $fileName);
                $this->libImage->delete($targetDir, $fileName);
                throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
        return true;
    }

    /**
     * @param string $currentPath
     * @param string $targetDir
     * @param bool $overwrite
     * @return bool
     * @throws ImagesException
     * @throws FilesException
     */
    public function move(string $currentPath, string $targetDir, bool $overwrite = false): bool
    {
        $origDir = Stuff::removeEndingSlash(Stuff::directory($currentPath));
        $fileName = Stuff::filename($currentPath);
        $targetDir = Stuff::removeEndingSlash($targetDir);
        try {
            $this->libImage->move($fileName, $origDir, $targetDir, $overwrite);
        } catch (FilesException $ex) {
            throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
        }
        if ($this->libThumb->isHere($currentPath)) {
            try {
                $this->libThumb->move($fileName, $origDir, $targetDir, $overwrite);
            } catch (FilesException $ex) {
                $this->libImage->move($fileName, $targetDir, $origDir);
                throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
        if ($this->libDesc->isHere($currentPath)) {
            try {
                $this->libDesc->move($fileName, $origDir, $targetDir, $overwrite);
            } catch (FilesException $ex) {
                $this->libThumb->move($fileName, $targetDir, $origDir);
                $this->libImage->move($fileName, $targetDir, $origDir);
                throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
        return true;
    }

    /**
     * @param string $currentPath
     * @param string $targetName
     * @param bool $overwrite
     * @return bool
     * @throws ImagesException
     * @throws FilesException
     */
    public function rename(string $currentPath, string $targetName, bool $overwrite = false): bool
    {
        $origDir = Stuff::removeEndingSlash(Stuff::directory($currentPath));
        $fileName = Stuff::filename($currentPath);
        try {
            $this->libImage->rename($origDir, $fileName, $targetName, $overwrite);
        } catch (FilesException $ex) {
            throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
        }
        if ($this->libThumb->isHere($currentPath)) {
            try {
                $this->libThumb->rename($origDir, $fileName, $targetName, $overwrite);
            } catch (FilesException $ex) {
                $this->libImage->rename($origDir, $targetName, $fileName);
                throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
        if ($this->libDesc->isHere($currentPath)) {
            try {
                $this->libDesc->rename($origDir, $fileName, $targetName, $overwrite);
            } catch (FilesException $ex) {
                $this->libThumb->rename($origDir, $targetName, $fileName);
                $this->libImage->rename($origDir, $targetName, $fileName);
                throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
        return true;
    }

    /**
     * @param string $path
     * @return bool
     * @throws FilesException
     */
    public function delete(string $path): bool
    {
        $origDir = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);

        $this->libDesc->delete($origDir, $fileName);
        $this->libThumb->delete($origDir, $fileName);
        $this->libImage->delete($origDir, $fileName);
        return true;
    }
}
