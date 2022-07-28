<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_files\Extended\Processor;
use kalanis\kw_files\FilesException;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_paths\Stuff;


/**
 * Class Image
 * Main image itself
 * @package kalanis\kw_images\Files
 */
class Image extends AFiles
{
    use TSizes;

    protected $maxWidth = 1024;
    protected $maxHeight = 1024;
    protected $maxFileSize = 10485760;
    protected $libGraphics = null;

    public function __construct(Processor $libProcessor, Graphics $libGraphics, array $params = [], ?IIMTranslations $lang = null)
    {
        parent::__construct($libProcessor, $lang);
        $this->libGraphics = $libGraphics;
        $this->maxWidth = !empty($params['max_width']) ? strval($params['max_width']) : $this->maxWidth;
        $this->maxHeight = !empty($params['max_height']) ? strval($params['max_height']) : $this->maxHeight;
        $this->maxFileSize = !empty($params['max_size']) ? strval($params['max_size']) : $this->maxFileSize;
    }

    /**
     * @param string $path
     * @param string $format
     * @throws FilesException
     * @return string|null
     */
    public function getCreated(string $path, string $format = 'Y-m-d H:i:s'): ?string
    {
        $created = $this->libProcessor->getNodeProcessor()->created($this->getPath($path));
        return (is_null($created)) ? null : date($format, $created);
    }

    /**
     * @param string $path
     * @throws FilesException
     */
    public function check(string $path): void
    {
        $size = $this->libProcessor->getNodeProcessor()->size($this->getPath($path));
        if (is_null($size)) {
            throw new FilesException($this->getLang()->imImageSizeExists());
        }
        if ($this->maxFileSize < $size) {
            throw new FilesException($this->getLang()->imImageSizeTooLarge());
        }
    }

    /**
     * @param string $path
     * @return bool
     * @throws ImagesException
     */
    public function processUploaded(string $path): bool
    {
        $this->libGraphics->load($path, $this->libProcessor->getWebRootDir() . $path);
        $sizes = $this->calculateSize($this->libGraphics->width(), $this->maxWidth, $this->libGraphics->height(), $this->maxHeight);
        $this->libGraphics->resample($sizes['width'], $sizes['height']);
        $this->libGraphics->save($path, $this->libProcessor->getWebRootDir() . $path);
        return true;
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
            Stuff::pathToArray($sourceDir) + [$fileName],
            Stuff::pathToArray($targetDir) + [$fileName],
            $overwrite,
            $this->getLang()->imImageCannotFind(),
            $this->getLang()->imImageAlreadyExistsHere(),
            $this->getLang()->imImageCannotRemoveOld(),
            $this->getLang()->imImageCannotCopyBase()
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
            Stuff::pathToArray($sourceDir) + [$fileName],
            Stuff::pathToArray($targetDir) + [$fileName],
            $overwrite,
            $this->getLang()->imImageCannotFind(),
            $this->getLang()->imImageAlreadyExistsHere(),
            $this->getLang()->imImageCannotRemoveOld(),
            $this->getLang()->imImageCannotMoveBase()
        );
    }

    /**
     * @param string $path
     * @param string $targetName
     * @param string $sourceName
     * @param bool $overwrite
     * @throws FilesException
     */
    public function rename(string $path, string $sourceName, string $targetName, bool $overwrite = false): void
    {
        $this->dataRename(
            Stuff::pathToArray($path) + [$sourceName],
            Stuff::pathToArray($path) + [$targetName],
            $overwrite,
            $this->getLang()->imImageCannotFind(),
            $this->getLang()->imImageAlreadyExistsHere(),
            $this->getLang()->imImageCannotRemoveOld(),
            $this->getLang()->imImageCannotRenameBase()
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
        $this->dataRemove($whatPath, $this->getLang()->imImageCannotRemove());
    }

    public function getPath(string $path): array
    {
        return Stuff::pathToArray($path);
    }
}
