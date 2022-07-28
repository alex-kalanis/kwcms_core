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
    use TSizes;

    const FILE_TEMP = '.tmp';

    protected $maxWidth = 180;
    protected $maxHeight = 180;
    protected $libGraphics = null;

    public function __construct(Processor $libProcessor, Graphics $libGraphics, array $params = [], ?IIMTranslations $lang = null)
    {
        parent::__construct($libProcessor, $lang);
        $this->libGraphics = $libGraphics;
        $this->maxWidth = !empty($params['tmb_width']) ? strval($params['tmb_width']) : $this->maxWidth;
        $this->maxHeight = !empty($params['tmb_height']) ? strval($params['tmb_height']) : $this->maxHeight;
    }

    /**
     * @param string $path
     * @throws ImagesException
     */
    public function create(string $path): void
    {
        $thumb = $this->libProcessor->getWebRootDir() . $this->getPath($path);
        $tempThumb = $thumb . static::FILE_TEMP;
        if ($this->libProcessor->isFile($thumb)) {
            if (!rename($thumb, $tempThumb)) {
                // @codeCoverageIgnoreStart
                throw new ImagesException($this->getLang()->imThumbCannotRemoveCurrent());
            }
            // @codeCoverageIgnoreEnd
        }
        try {
            $this->libGraphics->load($path, $this->libProcessor->getWebRootDir() . $path);
            $sizes = $this->calculateSize($this->libGraphics->width(), $this->maxWidth, $this->libGraphics->height(), $this->maxHeight);
            $this->libGraphics->resample($sizes['width'], $sizes['height']);
            $this->libGraphics->save($thumb, $thumb);
        } catch (ImagesException $ex) {
            if ($this->libProcessor->isFile($tempThumb) && !rename($tempThumb, $thumb)) {
                // @codeCoverageIgnoreStart
                throw new ImagesException($this->getLang()->imThumbCannotRestore());
            }
            // @codeCoverageIgnoreEnd
            throw $ex;
        }
        if ($this->libProcessor->isFile($tempThumb) && !unlink($tempThumb)) {
            // @codeCoverageIgnoreStart
            throw new ImagesException($this->getLang()->imThumbCannotRemoveOld());
        }
        // @codeCoverageIgnoreEnd
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
