<?php

namespace kalanis\kw_images;


use kalanis\kw_files\Extended\Config;
use kalanis\kw_files\FilesException;
use kalanis\kw_images\Graphics\ThumbConfig;
use kalanis\kw_paths\Stuff;


/**
 * Class Files
 * Operations over files
 * @package kalanis\kw_images
 * @todo: back to drawing board - separate by operations or their targets?
 * @deprecated remove because it's in kw_images/Content
 */
class Files
{
    protected $libImage = null;
    protected $libThumb = null;
    protected $libDesc = null;
    protected $libDirDesc = null;
    protected $libDirThumb = null;
    protected $libGraphics = null;

    public function __construct(Graphics $graphics, Sources\Image $image, Sources\Thumb $thumb, Sources\Desc $desc, Sources\DirDesc $dirDesc, Sources\DirThumb $dirThumb)
    {
        $this->libImage = $image;
        $this->libThumb = $thumb;
        $this->libDesc = $desc;
        $this->libDirDesc = $dirDesc;
        $this->libDirThumb = $dirThumb;
        $this->libGraphics = $graphics;
    }

    /**
     * @param ThumbConfig $tConfig
     * @param string[] $wantedPath
     * @param string $sourcePath
     * @param string $description
     * @param bool $hasThumb
     * @throws FilesException
     * @throws ImagesException
     * @return bool
     * @todo: rewrite to use things from both graphics part running locally and "remote" storage
     */
    public function add(ThumbConfig $tConfig, array $wantedPath, string $sourcePath = '', string $description = '', bool $hasThumb = true): bool
    {
        $origDir = array_slice($wantedPath, 0, -1);
        $fileName = array_slice($wantedPath, -1, 1);
        $fileName = reset($fileName);

        $this->libGraphics->check($sourcePath);
        $this->libGraphics->resize($sourcePath, $fileName);
        $uploaded = @file_get_contents($sourcePath);
        if (false === $uploaded) {
            return false;
        }
        $this->libImage->set($wantedPath, $uploaded);

        $this->libThumb->delete(Stuff::arrayToPath($origDir), $fileName);
        if ($hasThumb) {
            $this->createThumb(Stuff::arrayToPath($wantedPath), $tConfig);
        }

        if (!empty($description)) {
            $this->libDesc->set(Stuff::arrayToPath($wantedPath), $description);
        } else {
            $this->libDesc->delete(Stuff::arrayToPath($origDir), $fileName);
        }

        return true;
    }

    /**
     * @param string $path
     * @param Config $config
     * @param ThumbConfig $tConfig
     * @throws FilesException
     * @throws ImagesException
     */
    public function createDirThumb(string $path, Config $config, ThumbConfig $tConfig): void
    {
        $dir = Stuff::directory($path);
        $file = Stuff::filename($path);
        $tempFile = $config->getDescFile() . $config->getThumbExt();
        $backupFile = $config->getDescFile() . $config->getThumbExt() . $config->getThumbTemp();
        $backupPath = $dir . DIRECTORY_SEPARATOR . $backupFile;

        if ($this->libDirThumb->isHere($path)) {
            if (!$this->libThumb->rename($path, $tempFile, $backupFile)) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getLang()->imDirThumbCannotRemoveCurrent());
            }
            // @codeCoverageIgnoreEnd
        }
        try {
            if (!$this->libThumb->isHere($path)) {
                $this->createThumb($path, $tConfig);
                $this->libDirThumb->set($path, $this->libThumb->get($path));
                $this->libThumb->delete($dir, $file);
            } else {
                $this->libDirThumb->set($path, $this->libThumb->get($path));
            }
        } catch (FilesException $ex) {
            if ($this->libThumb->isHere($path) && !$this->libThumb->rename($dir, $backupFile, $tempFile)) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getLang()->imDirThumbCannotRestore());
            }
            // @codeCoverageIgnoreEnd
            throw $ex;
        }
        if ($this->libThumb->isHere($backupPath) && !$this->libThumb->delete($dir, $backupFile)) {
            // @codeCoverageIgnoreStart
            throw new FilesException($this->getLang()->imDirThumbCannotRemoveOld());
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $path
     * @param ThumbConfig $config
     * @throws FilesException
     * @throws ImagesException
     */
    public function createThumb(string $path, ThumbConfig $config): void
    {
        $dir = Stuff::directory($path);
        $file = Stuff::filename($path);
        $tempFile = $config->getTempDir() . $this->randomName();
        $tempPath = $path . $config->getTempExt();
        $backupFile = $file . $config->getTempExt();

        // move old one
        if ($this->libThumb->isHere($path)) {
            if (!$this->libThumb->rename($dir, $file, $backupFile)) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getLang()->imThumbCannotRemoveCurrent());
            }
            // @codeCoverageIgnoreEnd
        }

        try {
            // get from the storage
            $source = $this->libImage->get($path);
            if (false === @file_put_contents($tempFile, $source)) {
                throw new FilesException($this->getLang()->imThumbCannotCopyBase());  ###!!! correct translation
            }

            // now process libraries locally
            $this->libGraphics->resize($tempFile, $path);

            // return result to the storage as new file
            $result = @file_get_contents($tempFile);
            if (false === $result) {
                throw new FilesException($this->getLang()->imThumbCannotCopyBase());  ###!!! correct translation
            }
            $this->libThumb->set($path, $result);

        } catch (ImagesException $ex) {
            if ($this->libThumb->isHere($tempPath) && !$this->libThumb->rename($dir, $backupFile, $file)) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getLang()->imThumbCannotRestore());
            }
            // @codeCoverageIgnoreEnd
            throw $ex;
        }
        if ($this->libThumb->isHere($tempPath) && !$this->libThumb->delete($dir, $backupFile)) {
            // @codeCoverageIgnoreStart
            throw new FilesException($this->getLang()->imThumbCannotRemoveOld());
        }
        // @codeCoverageIgnoreEnd
    }

    protected function randomName(): string
    {
        return uniqid('tmp_tmb_');
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

    public function getLibGraphics(): Graphics
    {
        return $this->libGraphics;
    }

    public function getLibImage(): Sources\Image
    {
        return $this->libImage;
    }

    public function getLibThumb(): Sources\Thumb
    {
        return $this->libThumb;
    }

    public function getLibDesc(): Sources\Desc
    {
        return $this->libDesc;
    }

    public function getLibDirDesc(): Sources\DirDesc
    {
        return $this->libDirDesc;
    }

    public function getLibDirThumb(): Sources\DirThumb
    {
        return $this->libDirThumb;
    }
}
