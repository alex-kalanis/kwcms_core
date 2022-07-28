<?php

namespace kalanis\kw_images;


use kalanis\kw_files\FilesException;
use kalanis\kw_paths\Stuff;


/**
 * Class Files
 * Operations over files
 * @package kalanis\kw_images
 */
class Files
{
    protected $libImage = null;
    protected $libThumb = null;
    protected $libDesc = null;
    protected $libDirDesc = null;
    protected $libDirThumb = null;

    public function __construct(Files\Image $image, Files\Thumb $thumb, Files\Desc $desc, Files\DirDesc $dirDesc, Files\DirThumb $dirThumb)
    {
        $this->libImage = $image;
        $this->libThumb = $thumb;
        $this->libDesc = $desc;
        $this->libDirDesc = $dirDesc;
        $this->libDirThumb = $dirThumb;
    }

    /**
     * @param string[] $currentPath
     * @param string $description
     * @param bool $hasThumb
     * @throws FilesException
     * @throws ImagesException
     * @return bool
     */
    public function add(array $currentPath, string $description = '', bool $hasThumb = true): bool
    {
        $origDir = Stuff::removeEndingSlash(Stuff::directory($currentPath));
        $fileName = Stuff::filename($currentPath);

        $this->libImage->check($currentPath);
        $this->libImage->processUploaded($currentPath);

        $this->libThumb->delete($origDir, $fileName);
        if ($hasThumb) {
            $this->libThumb->create($currentPath);
        }

        if (!empty($description)) {
            $this->libDesc->set($currentPath, $description);
        } else {
            $this->libDesc->delete($origDir, $fileName);
        }

        return true;
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
        } catch (ImagesException $ex) {
            return false;
        } catch (FilesException $ex) {
            throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
        }
        if ($this->libThumb->isHere($currentPath)) {
            try {
                $this->libThumb->copy($fileName, $origDir, $targetDir, $overwrite);
            } catch (ImagesException $ex) {
                $this->libImage->delete($targetDir, $fileName);
                return false;
            } catch (FilesException $ex) {
                $this->libImage->delete($targetDir, $fileName);
                throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
        if ($this->libDesc->isHere($currentPath)) {
            try {
                $this->libDesc->copy($fileName, $origDir, $targetDir, $overwrite);
            } catch (ImagesException $ex) {
                $this->libThumb->delete($targetDir, $fileName);
                $this->libImage->delete($targetDir, $fileName);
                return false;
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
        } catch (ImagesException $ex) {
            return false;
        } catch (FilesException $ex) {
            throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
        }
        if ($this->libThumb->isHere($currentPath)) {
            try {
                $this->libThumb->move($fileName, $origDir, $targetDir, $overwrite);
            } catch (ImagesException $ex) {
                $this->libImage->move($fileName, $targetDir, $origDir);
                return false;
            } catch (FilesException $ex) {
                $this->libImage->move($fileName, $targetDir, $origDir);
                throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
        if ($this->libDesc->isHere($currentPath)) {
            try {
                $this->libDesc->move($fileName, $origDir, $targetDir, $overwrite);
            } catch (ImagesException $ex) {
                $this->libThumb->move($fileName, $targetDir, $origDir);
                $this->libImage->move($fileName, $targetDir, $origDir);
                return false;
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
        } catch (ImagesException $ex) {
            return false;
        } catch (FilesException $ex) {
            throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
        }
        if ($this->libThumb->isHere($currentPath)) {
            try {
                $this->libThumb->rename($origDir, $fileName, $targetName, $overwrite);
            } catch (ImagesException $ex) {
                $this->libImage->rename($origDir, $targetName, $fileName);
                return false;
            } catch (FilesException $ex) {
                $this->libImage->rename($origDir, $targetName, $fileName);
                throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
        if ($this->libDesc->isHere($currentPath)) {
            try {
                $this->libDesc->rename($origDir, $fileName, $targetName, $overwrite);
            } catch (ImagesException $ex) {
                $this->libThumb->rename($origDir, $targetName, $fileName);
                $this->libImage->rename($origDir, $targetName, $fileName);
                return false;
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

    public function getLibImage(): Files\Image
    {
        return $this->libImage;
    }

    public function getLibThumb(): Files\Thumb
    {
        return $this->libThumb;
    }

    public function getLibDesc(): Files\Desc
    {
        return $this->libDesc;
    }

    public function getLibDirDesc(): Files\DirDesc
    {
        return $this->libDirDesc;
    }

    public function getLibDirThumb(): Files\DirThumb
    {
        return $this->libDirThumb;
    }
}
