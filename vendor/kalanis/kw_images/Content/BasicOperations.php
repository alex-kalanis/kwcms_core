<?php

namespace kalanis\kw_images\Content;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\Sources;


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
     * @param string[] $currentPath
     * @param string[] $targetDir
     * @param bool $overwrite
     * @throws FilesException
     * @return bool
     */
    public function copy(array $currentPath, array $targetDir, bool $overwrite = false): bool
    {
        $fullPath = array_values($currentPath);
        $fileName = strval(array_pop($currentPath));

        $this->libImage->copy($fileName, $currentPath, $targetDir, $overwrite);
        if ($this->libThumb->isHere($fullPath)) {
            try {
                $this->libThumb->copy($fileName, $currentPath, $targetDir, $overwrite);
            } catch (FilesException $ex) {
                $this->libImage->delete($targetDir, $fileName);
                throw $ex;
            }
        }
        if ($this->libDesc->isHere($fullPath)) {
            try {
                $this->libDesc->copy($fileName, $currentPath, $targetDir, $overwrite);
            } catch (FilesException $ex) {
                $this->libThumb->delete($targetDir, $fileName);
                $this->libImage->delete($targetDir, $fileName);
                throw $ex;
            }
        }
        return true;
    }

    /**
     * @param string[] $currentPath
     * @param string[] $targetDir
     * @param bool $overwrite
     * @throws FilesException
     * @return bool
     */
    public function move(array $currentPath, array $targetDir, bool $overwrite = false): bool
    {
        $fullPath = array_values($currentPath);
        $fileName = strval(array_pop($currentPath));

        $this->libImage->move($fileName, $currentPath, $targetDir, $overwrite);
        if ($this->libThumb->isHere($fullPath)) {
            try {
                $this->libThumb->move($fileName, $currentPath, $targetDir, $overwrite);
            } catch (FilesException $ex) {
                $this->libImage->move($fileName, $targetDir, $currentPath);
                throw $ex;
            }
        }
        if ($this->libDesc->isHere($fullPath)) {
            try {
                $this->libDesc->move($fileName, $currentPath, $targetDir, $overwrite);
            } catch (FilesException $ex) {
                $this->libThumb->move($fileName, $targetDir, $currentPath);
                $this->libImage->move($fileName, $targetDir, $currentPath);
                throw $ex;
            }
        }
        return true;
    }

    /**
     * @param string[] $currentPath
     * @param string $targetName
     * @param bool $overwrite
     * @throws FilesException
     * @return bool
     */
    public function rename(array $currentPath, string $targetName, bool $overwrite = false): bool
    {
        $fullPath = array_values($currentPath);
        $fileName = strval(array_pop($currentPath));

        $this->libImage->rename($currentPath, $fileName, $targetName, $overwrite);
        if ($this->libThumb->isHere($fullPath)) {
            try {
                $this->libThumb->rename($currentPath, $fileName, $targetName, $overwrite);
            } catch (FilesException $ex) {
                $this->libImage->rename($currentPath, $targetName, $fileName);
                throw $ex;
            }
        }
        if ($this->libDesc->isHere($fullPath)) {
            try {
                $this->libDesc->rename($currentPath, $fileName, $targetName, $overwrite);
            } catch (FilesException $ex) {
                $this->libThumb->rename($currentPath, $targetName, $fileName);
                $this->libImage->rename($currentPath, $targetName, $fileName);
                throw $ex;
            }
        }
        return true;
    }

    /**
     * @param string[] $path
     * @throws FilesException
     * @return bool
     */
    public function delete(array $path): bool
    {
        $fileName = strval(array_pop($path));

        $r1 = $this->libDesc->delete($path, $fileName);
        $r2 = $this->libThumb->delete($path, $fileName);
        $r3 = $this->libImage->delete($path, $fileName);
        return $r1 && $r2 && $r3;
    }
}
