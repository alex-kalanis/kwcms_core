<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_paths\Extras\ExtendDir;
use kalanis\kw_paths\PathsException;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;


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

    public function __construct(ExtendDir $libExtendDir, Graphics $libGraphics, array $params = [], ?IIMTranslations $lang = null)
    {
        parent::__construct($libExtendDir, $lang);
        $this->libGraphics = $libGraphics;
        $this->maxWidth = !empty($params['max_width']) ? strval($params['max_width']) : $this->maxWidth;
        $this->maxHeight = !empty($params['max_height']) ? strval($params['max_height']) : $this->maxHeight;
        $this->maxFileSize = !empty($params['max_size']) ? strval($params['max_size']) : $this->maxFileSize;
    }

    public function getCreated(string $path, string $format = 'Y-m-d H:i:s'): ?string
    {
        $created = filemtime($this->libExtendDir->getWebRootDir() . $this->getPath($path));
        return (false === $created) ? null : date($format, $created);
    }

    /**
     * @param string $path
     * @throws ImagesException
     */
    public function check(string $path): void
    {
        $size = @filesize($this->libExtendDir->getWebRootDir() . $path);
        if (false === $size) {
            throw new ImagesException($this->getLang()->imImageSizeExists());
        }
        if ($this->maxFileSize < $size) {
            throw new ImagesException($this->getLang()->imImageSizeTooLarge());
        }
    }

    /**
     * @param string $path
     * @return bool
     * @throws ImagesException
     */
    public function processUploaded(string $path): bool
    {
        $this->libGraphics->load($this->libExtendDir->getWebRootDir() . $path);
        $sizes = $this->calculateSize($this->libGraphics->width(), $this->maxWidth, $this->libGraphics->height(), $this->maxHeight);
        $this->libGraphics->resample($sizes['width'], $sizes['height']);
        $this->libGraphics->save($this->libExtendDir->getWebRootDir() . $path);
        return true;
    }

    /**
     * @param string $fileName
     * @param string $sourceDir
     * @param string $targetDir
     * @param bool $overwrite
     * @throws PathsException
     * @throws ImagesException
     */
    public function copy(string $fileName, string $sourceDir, string $targetDir, bool $overwrite = false): void
    {
        $sourcePath = $this->libExtendDir->getWebRootDir() . $sourceDir;
        $targetPath = $this->libExtendDir->getWebRootDir() . $targetDir;

        $this->checkWritable($targetPath);
        $this->dataCopy(
            $sourcePath . DIRECTORY_SEPARATOR . $fileName,
            $targetPath . DIRECTORY_SEPARATOR . $fileName,
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
     * @throws PathsException
     * @throws ImagesException
     */
    public function move(string $fileName, string $sourceDir, string $targetDir, bool $overwrite = false): void
    {
        $sourcePath = $this->libExtendDir->getWebRootDir() . $sourceDir;
        $targetPath = $this->libExtendDir->getWebRootDir() . $targetDir;

        $this->checkWritable($targetPath);
        $this->dataRename(
            $sourcePath . DIRECTORY_SEPARATOR . $fileName,
            $targetPath . DIRECTORY_SEPARATOR . $fileName,
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
     * @throws PathsException
     * @throws ImagesException
     */
    public function rename(string $path, string $sourceName, string $targetName, bool $overwrite = false): void
    {
        $whatPath = $this->libExtendDir->getWebRootDir() . $path;

        $this->checkWritable($whatPath);
        $this->dataRename(
            $whatPath . DIRECTORY_SEPARATOR . $sourceName,
            $whatPath . DIRECTORY_SEPARATOR . $targetName,
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
     * @throws ImagesException
     */
    public function delete(string $sourceDir, string $fileName): void
    {
        $whatPath = $this->libExtendDir->getWebRootDir() . $this->getPath($sourceDir . DIRECTORY_SEPARATOR . $fileName);
        $this->dataRemove($whatPath, $this->getLang()->imImageCannotRemove());
    }

    public function getPath(string $path): string
    {
        return $path; // no modifications need
    }
}
