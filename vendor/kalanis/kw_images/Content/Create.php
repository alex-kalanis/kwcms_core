<?php

namespace kalanis\kw_images\Content;


use kalanis\kw_files\FilesException;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_images\Sources;
use kalanis\kw_images\TLang;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Stuff;


/**
 * Class Create
 * Create specific content
 * @package kalanis\kw_images\Content
 */
class Create
{
    use TLang;

    /** @var Sources\Image */
    protected $libImage = null;
    /** @var Sources\Thumb */
    protected $libThumb = null;
    /** @var Sources\Desc */
    protected $libDesc = null;
    /** @var Graphics */
    protected $libGraphics = null;
    /** @var Graphics\ThumbConfig */
    protected $thumbConfig = null;

    public function __construct(Graphics $graphics, Graphics\ThumbConfig $thumbConfig, Sources\Image $image, Sources\Thumb $thumb, Sources\Desc $desc, ?IIMTranslations $lang = null)
    {
        $this->setLang($lang);
        $this->libImage = $image;
        $this->libThumb = $thumb;
        $this->libDesc = $desc;
        $this->libGraphics = $graphics;
        $this->thumbConfig = $thumbConfig;
    }

    /**
     * @param string $name
     * @throws FilesException
     * @return string
     */
    public function findFreeName(string $name): string
    {
        $name = Stuff::canonize($name);
        $ext = Stuff::fileExt($name);
        if (0 < mb_strlen($ext)) {
            $ext = IPaths::SPLITTER_DOT . $ext;
        }
        $fileName = Stuff::fileBase($name);
        return $this->libImage->findFreeName($fileName, $ext);
    }

    /**
     * @param string[] $wantedPath
     * @param string $sourcePath
     * @param string $description
     * @param bool $hasThumb
     * @throws FilesException
     * @throws ImagesException
     * @return bool
     * @todo: rewrite to use things from both graphics part running locally and "remote" storage
     */
    public function image(array $wantedPath, string $sourcePath = '', string $description = '', bool $hasThumb = true): bool
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
            $this->thumb(Stuff::arrayToPath($wantedPath));
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
     * @throws FilesException
     * @throws ImagesException
     */
    public function thumb(string $path): void
    {
        $dir = Stuff::directory($path);
        $file = Stuff::filename($path);
        $tempFile = $this->thumbConfig->getTempDir() . $this->randomName();
        $tempPath = $path . $this->thumbConfig->getTempExt();
        $backupFile = $file . $this->thumbConfig->getTempExt();

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
}
