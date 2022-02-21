<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_paths\Extras\ExtendDir;
use kalanis\kw_paths\PathsException;
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

    public function __construct(ExtendDir $libExtendDir, Graphics $libGraphics, array $params = [], ?IIMTranslations $lang = null)
    {
        parent::__construct($libExtendDir, $lang);
        $this->libGraphics = $libGraphics;
        $this->maxWidth = !empty($params["tmb_width"]) ? strval($params["tmb_width"]) : $this->maxWidth;
        $this->maxHeight = !empty($params["tmb_height"]) ? strval($params["tmb_height"]) : $this->maxHeight;
    }

    /**
     * @param string $path
     * @throws ImagesException
     */
    public function create(string $path): void
    {
        $thumb = $this->libExtendDir->getWebRootDir() . $this->getPath($path);
        $tempThumb = $thumb . static::FILE_TEMP;
        if (is_file($thumb)) {
            if (!rename($thumb, $tempThumb)) {
                throw new ImagesException($this->getLang()->imThumbCannotRemoveCurrent());
            }
        }
        try {
            $this->libGraphics->load($this->libExtendDir->getWebRootDir() . $path);
            $sizes = $this->calculateSize($this->libGraphics->width(), $this->maxWidth, $this->libGraphics->height(), $this->maxHeight);
            $this->libGraphics->resample($sizes['width'], $sizes['height']);
            $this->libGraphics->save($thumb);
        } catch (ImagesException $ex) {
            if (is_file($tempThumb) && !rename($tempThumb, $thumb)) {
                throw new ImagesException($this->getLang()->imThumbCannotRestore());
            }
            throw $ex;
        }
        if (is_file($tempThumb) && !unlink($tempThumb)) {
            throw new ImagesException($this->getLang()->imThumbCannotRemoveOld());
        }
    }

    /**
     * @param string $fileName
     * @param string $sourceDir
     * @param string $targetDir
     * @param bool $overwrite
     * @return bool
     * @throws PathsException
     * @throws ImagesException
     */
    public function copy(string $fileName, string $sourceDir, string $targetDir, bool $overwrite = false): bool
    {
        $sourcePath = $this->libExtendDir->getWebRootDir() . $sourceDir . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir();
        $targetPath = $this->libExtendDir->getWebRootDir() . $targetDir . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir();

        $this->checkWritable($targetPath);
        $this->dataCopy(
            $sourcePath . DIRECTORY_SEPARATOR . $fileName,
            $targetPath . DIRECTORY_SEPARATOR . $fileName,
            $overwrite,
            $this->getLang()->imThumbCannotFind(),
            $this->getLang()->imThumbAlreadyExistsHere(),
            $this->getLang()->imThumbCannotRemoveOld(),
            $this->getLang()->imThumbCannotCopyBase()
        );

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
    public function move(string $fileName, string $sourceDir, string $targetDir, bool $overwrite = false): void
    {
        $sourcePath = $this->libExtendDir->getWebRootDir() . $sourceDir . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir();
        $targetPath = $this->libExtendDir->getWebRootDir() . $targetDir . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir();

        $this->checkWritable($targetPath);
        $this->dataRename(
            $sourcePath . DIRECTORY_SEPARATOR . $fileName,
            $targetPath . DIRECTORY_SEPARATOR . $fileName,
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
     * @throws PathsException
     * @throws ImagesException
     */
    public function rename(string $path, string $sourceName, string $targetName, bool $overwrite = false): void
    {
        $whatPath = $this->libExtendDir->getWebRootDir() . $path . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir();

        $this->checkWritable($whatPath);
        $this->dataRename(
            $whatPath . DIRECTORY_SEPARATOR . $sourceName,
            $whatPath . DIRECTORY_SEPARATOR . $targetName,
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
     * @throws ImagesException
     */
    public function delete(string $sourceDir, string $fileName): void
    {
        $whatPath = $this->libExtendDir->getWebRootDir() . $this->getPath($sourceDir . DIRECTORY_SEPARATOR . $fileName);
        $this->dataRemove($whatPath, $this->getLang()->imThumbCannotRemove());
    }

    public function getPath(string $path): string
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);
        return $filePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir() . DIRECTORY_SEPARATOR . $fileName;
    }
}
