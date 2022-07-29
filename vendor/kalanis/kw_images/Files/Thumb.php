<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_files\Extended\Processor;
use kalanis\kw_files\FilesException;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_paths\Stuff;


/**
 * Class Thumb
 * File thumbnail
 * @package kalanis\kw_images\Files
 */
class Thumb extends AFiles
{
    /** @var Graphics\Processor */
    protected $libGraphics = null;
    /** @var Graphics\ThumbConfig */
    protected $libConf = null;

    public function __construct(Processor $libProcessor, Graphics\Processor $libGraphics, Graphics\ThumbConfig $thumbConfig, ?IIMTranslations $lang = null)
    {
        parent::__construct($libProcessor, $lang);
        $this->libGraphics = $libGraphics;
        $this->libConf = $thumbConfig;
    }

    /**
     * @param string $path
     * @param string|resource $content
     * @throws FilesException
     * @return bool
     */
    public function save(string $path, $content): bool
    {
        $this->libProcessor->getFileProcessor()->saveFile($this->getPath($path), $content);
        return true;
    }

    /**
     * @param string $path
     * @throws FilesException
     * @return string|resource
     */
    public function load(string $path)
    {
        return $this->libProcessor->getFileProcessor()->readFile($this->getPath($path));
    }

    /**
     * @param string $path
     * @throws FilesException
     * @throws ImagesException
     * @todo: out
     */
    public function create(string $path): void
    {
        $tempFile = $this->libConf->getTempDir() . $this->randomName();
        $thumb = $this->getPath($path);
        $tempThumb = $this->getPath($path . $this->libConf->getTempExt());
        $image = $this->getImagePath($path);

        // move old one
        if ($this->libProcessor->getNodeProcessor()->isFile($thumb)) {
            if (!$this->libProcessor->getFileProcessor()->moveFile($thumb, $tempThumb)) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getLang()->imThumbCannotRemoveCurrent());
            }
            // @codeCoverageIgnoreEnd
        }

        try {
            // get from the storage
            $source = $this->libProcessor->getFileProcessor()->readFile($image);
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
            $this->libProcessor->getFileProcessor()->saveFile($thumb, $result);

        } catch (ImagesException $ex) {
            if ($this->libProcessor->getNodeProcessor()->isFile($tempThumb) && !$this->libProcessor->getFileProcessor()->moveFile($tempThumb, $thumb)) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getLang()->imThumbCannotRestore());
            }
            // @codeCoverageIgnoreEnd
            throw $ex;
        }
        if ($this->libProcessor->getNodeProcessor()->isFile($tempThumb) && !$this->libProcessor->getFileProcessor()->deleteFile($tempThumb)) {
            // @codeCoverageIgnoreStart
            throw new FilesException($this->getLang()->imThumbCannotRemoveOld());
        }
        // @codeCoverageIgnoreEnd
    }

    protected function randomName(): string
    {
        return uniqid('tmp_tmb_');
    }

    public function getImagePath(string $path): array
    {
        return Stuff::pathToArray($path);
    }

    /**
     * @param string $fileName
     * @param string $sourceDir
     * @param string $targetDir
     * @param bool $overwrite
     * @throws FilesException
     */
    public function copy(string $fileName, string $sourceDir, string $targetDir, bool $overwrite = false): void
    {
        $this->dataCopy(
            Stuff::pathToArray($sourceDir) + [$this->libProcessor->getConfig()->getThumbDir(), $fileName],
            Stuff::pathToArray($targetDir) + [$this->libProcessor->getConfig()->getThumbDir(), $fileName],
            $overwrite,
            $this->getLang()->imThumbCannotFind(),
            $this->getLang()->imThumbAlreadyExistsHere(),
            $this->getLang()->imThumbCannotRemoveOld(),
            $this->getLang()->imThumbCannotCopyBase()
        );
    }

    /**
     * @param string $fileName
     * @param string $sourceDir
     * @param string $targetDir
     * @param bool $overwrite
     * @throws FilesException
     */
    public function move(string $fileName, string $sourceDir, string $targetDir, bool $overwrite = false): void
    {
        $this->dataRename(
            Stuff::pathToArray($sourceDir) + [$this->libProcessor->getConfig()->getThumbDir(), $fileName],
            Stuff::pathToArray($targetDir) + [$this->libProcessor->getConfig()->getThumbDir(), $fileName],
            $overwrite,
            $this->getLang()->imThumbCannotFind(),
            $this->getLang()->imThumbAlreadyExistsHere(),
            $this->getLang()->imThumbCannotRemoveOld(),
            $this->getLang()->imThumbCannotMoveBase()
        );
    }

    /**
     * @param string $path
     * @param string $sourceName
     * @param string $targetName
     * @param bool $overwrite
     * @throws FilesException
     */
    public function rename(string $path, string $sourceName, string $targetName, bool $overwrite = false): void
    {
        $this->dataRename(
            Stuff::pathToArray($path) + [$this->libProcessor->getConfig()->getThumbDir(), $sourceName],
            Stuff::pathToArray($path) + [$this->libProcessor->getConfig()->getThumbDir(), $targetName],
            $overwrite,
            $this->getLang()->imThumbCannotFind(),
            $this->getLang()->imThumbAlreadyExistsHere(),
            $this->getLang()->imThumbCannotRemoveOld(),
            $this->getLang()->imThumbCannotRenameBase()
        );
    }

    /**
     * @param string $sourceDir
     * @param string $fileName
     * @throws FilesException
     */
    public function delete(string $sourceDir, string $fileName): void
    {
        $whatPath = $this->getPath($sourceDir . DIRECTORY_SEPARATOR . $fileName);
        $this->dataRemove($whatPath, $this->getLang()->imThumbCannotRemove());
    }

    public function getPath(string $path): array
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);
        return Stuff::pathToArray($filePath) + [$this->libProcessor->getConfig()->getThumbDir(), $fileName];
    }
}
