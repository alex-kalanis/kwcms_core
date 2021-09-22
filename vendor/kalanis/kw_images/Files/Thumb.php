<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_extras\ExtendDir;
use kalanis\kw_extras\ExtrasException;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_paths\Stuff;


/**
 * Class Thumb
 * File thumbnail
 * @package kalanis\kw_images\Files
 */
class Thumb extends AFiles
{
    const FILE_TEMP = '.tmp';

    protected $maxWidth = 180;
    protected $maxHeight = 180;
    protected $libGraphics = null;

    public function __construct(ExtendDir $libExtendDir, Graphics $libGraphics, array $params = [])
    {
        parent::__construct($libExtendDir);
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
        $thumb = $this->getPath($path);
        $tempThumb = $thumb . static::FILE_TEMP;
        if (is_file($thumb)) {
            if (!rename($thumb, $tempThumb)) {
                throw new ImagesException('Cannot remove current thumb!');
            }
        }
        try {
            $this->libGraphics->load($path);
            $sizes = $this->calculateSize($this->libGraphics->width(), $this->maxWidth, $this->libGraphics->height(), $this->maxHeight);
            $this->libGraphics->resample($sizes['width'], $sizes['height']);
            $this->libGraphics->save($thumb);
        } catch (ImagesException $ex) {
            if (!rename($tempThumb, $thumb)) {
                throw new ImagesException('Cannot remove current thumb back!');
            }
            throw $ex;
        }
        if (is_file($tempThumb) && !unlink($tempThumb)) {
            throw new ImagesException('Cannot remove old thumb!');
        }
    }

    /**
     * @param string $path
     * @param string $targetDir
     * @param bool $overwrite
     * @return bool
     * @throws ExtrasException
     * @throws ImagesException
     */
    public function copy(string $path, string $targetDir, bool $overwrite = false): bool
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);

        $sourcePath = $this->libExtendDir->getWebRootDir() . $filePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir();
        $targetPath = $this->libExtendDir->getWebRootDir() . Stuff::removeEndingSlash($targetDir) . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir();

        $this->checkWritable($targetPath);
        $this->dataCopy(
            $sourcePath . DIRECTORY_SEPARATOR . $fileName,
            $targetPath . DIRECTORY_SEPARATOR . $fileName,
            $overwrite,
            'Cannot find that thumb.',
            'Thumb with the same name already exists here.',
            'Cannot remove old thumb.',
            'Cannot copy base thumb.'
        );

        return true;
    }

    /**
     * @param string $path
     * @param string $targetDir
     * @param bool $overwrite
     * @throws ExtrasException
     * @throws ImagesException
     */
    public function move(string $path, string $targetDir, bool $overwrite = false): void
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);

        $sourcePath = $this->libExtendDir->getWebRootDir() . $filePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir();
        $targetPath = $this->libExtendDir->getWebRootDir() . Stuff::removeEndingSlash($targetDir) . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir();

        $this->checkWritable($targetPath);
        $this->dataRename(
            $sourcePath . DIRECTORY_SEPARATOR . $fileName,
            $targetPath . DIRECTORY_SEPARATOR . $fileName,
            $overwrite,
            'Cannot find that thumb.',
            'Thumb with the same name already exists here.',
            'Cannot remove old thumb.',
            'Cannot move base thumb.'
        );
    }

    /**
     * @param string $path
     * @param string $targetName
     * @param bool $overwrite
     * @throws ExtrasException
     * @throws ImagesException
     */
    public function rename(string $path, string $targetName, bool $overwrite = false): void
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);

        $whatPath = $this->libExtendDir->getWebRootDir() . $filePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir();

        $this->checkWritable($whatPath);
        $this->dataRename(
            $whatPath . DIRECTORY_SEPARATOR . $fileName,
            $whatPath . DIRECTORY_SEPARATOR . $targetName,
            $overwrite,
            'Cannot find that thumb.',
            'Thumb with the same name already exists here.',
            'Cannot remove old thumb.',
            'Cannot rename base thumb.'
        );
    }

    /**
     * @param string $path
     * @throws ImagesException
     */
    public function delete(string $path): void
    {
        $this->deleteFile($this->getPath($path), 'Cannot remove thumb!');
    }

    public function getPath(string $path): string
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);
        return $filePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir() . DIRECTORY_SEPARATOR . $fileName;
    }
}
