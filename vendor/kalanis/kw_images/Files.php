<?php

namespace kalanis\kw_images;


use kalanis\kw_extras\ExtendDir;
use kalanis\kw_extras\ExtrasException;
use kalanis\kw_paths\Stuff;


/**
 * Class Files
 * @package kalanis\kw_images
 *
 * @see libimages
 *
 * Tady jsou hratky s cestama - v Graphics je nacpany processing pro jednotlive podporovane formaty
 * Takze tady seskladam cesty, dam to Graphics na upravy a ziskam upravena data
 */
class Files
{
    const FILE_TEMP = '.tmp';

    protected $thumbMaxWidth = 180;
    protected $thumbMaxHeight = 180;
    protected $imageMaxWidth = 1024;
    protected $imageMaxHeight = 1024;
    protected $imageMaxFileSize = 1024;
    protected $libExtendDir = null;
    protected $libGraphics = null;

    public function __construct(ExtendDir $libExtendDir, Graphics $libGraphics, array $params = [])
    {
        $this->libExtendDir = $libExtendDir;
        $this->libGraphics = $libGraphics;
        $this->thumbMaxWidth = !empty($params["tmb_width"]) ? strval($params["tmb_width"]) : $this->thumbMaxWidth;
        $this->thumbMaxHeight = !empty($params["tmb_height"]) ? strval($params["tmb_height"]) : $this->thumbMaxHeight;
        $this->imageMaxWidth = !empty($params["max_width"]) ? strval($params["max_width"]) : $this->imageMaxWidth;
        $this->imageMaxHeight = !empty($params["max_height"]) ? strval($params["max_height"]) : $this->imageMaxHeight;
        $this->imageMaxFileSize = !empty($params["max_size"]) ? strval($params["max_size"]) : $this->imageMaxFileSize;
    }

    /**
     * @param string $path
     * @param string $content
     * @return bool
     * @throws ExtrasException
     */
    public function setDirDescription(string $path, string $content): bool
    {
        return $this->libExtendDir->setDirDescription($path, $content);
    }

    /**
     * @param string $path
     * @return string
     * @throws ExtrasException
     */
    public function getDirDescription(string $path): string
    {
        return $this->libExtendDir->getDirDescription($path);
    }

    public function getImageCreated(string $path, string $format = 'd.m.Y \@ H:i:s'): ?string
    {
        $created = filemtime($this->libExtendDir->getWebRootDir() . $path);
        return (false === $created) ? null : date($format, $created);
    }

    /**
     * @param string $path
     * @throws ImagesException
     */
    public function createImageThumb(string $path): void
    {
        $filePath = Stuff::directory($path);
        $fileName = Stuff::filename($path);
        $thumb = $filePath . $this->libExtendDir->getThumbDir() . DIRECTORY_SEPARATOR . $fileName;
        $tempThumb = $thumb . static::FILE_TEMP;
        if (is_file($thumb)) {
            if (!rename($thumb, $tempThumb)) {
                throw new ImagesException('Cannot remove current thumb!');
            }
        }
        try {
            $this->libGraphics->load($path);
            $sizes = $this->calculateSize($this->libGraphics->width(), $this->thumbMaxWidth, $this->libGraphics->height(), $this->thumbMaxHeight);
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

    protected function calculateSize(int $currentWidth, int $maxWidth, int $currentHeight, int $maxHeight): array
    {
        $newWidth = $currentWidth / $maxWidth;
        $newHeight = $currentHeight / $maxHeight;
        $ratio = max($newWidth, $newHeight); // due this it's necessary to pass all
        $ratio = max($ratio, 1.0);
        $newWidth = (int)($currentWidth / $ratio);
        $newHeight = (int)($currentHeight / $ratio);
        return ['width' => $newWidth, 'height' => $newHeight];
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

        $sourcePath = $this->libExtendDir->getWebRootDir() . $filePath;
        $targetPath = $this->libExtendDir->getWebRootDir() . Stuff::removeEndingSlash($targetDir);

        $this->checkWritable($targetPath);

        if (!is_file($sourcePath . DIRECTORY_SEPARATOR . $fileName)) {
            throw new ImagesException('Cannot find that file.');
        }

        if (is_file($targetPath . DIRECTORY_SEPARATOR . $fileName) && !$overwrite) {
            throw new ImagesException('File with the same name already exists here.');
        }

        $this->overwriteDataCopy(
            $sourcePath . DIRECTORY_SEPARATOR . $fileName,
            $targetPath . DIRECTORY_SEPARATOR . $fileName,
            'Cannot remove old image.',
            'Cannot copy base image.'
        );

        $this->overwriteDataCopy(
            $sourcePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt(),
            $targetPath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt(),
            'Cannot remove old description.',
            'Cannot copy description.'
        );

        $this->overwriteDataCopy(
            $sourcePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir() . DIRECTORY_SEPARATOR . $fileName,
            $targetPath . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir() . DIRECTORY_SEPARATOR . $fileName,
            'Cannot remove old thumb.',
            'Cannot copy thumb.'
        );
        return true;
    }

    /**
     * @param string $path
     * @param string $targetDir
     * @param bool $overwrite
     * @return bool
     * @throws ExtrasException
     * @throws ImagesException
     */
    public function move(string $path, string $targetDir, bool $overwrite = false): bool
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);

        $sourcePath = $this->libExtendDir->getWebRootDir() . $filePath;
        $targetPath = $this->libExtendDir->getWebRootDir() . Stuff::removeEndingSlash($targetDir);

        $this->checkWritable($targetPath);

        if (!is_file($sourcePath . DIRECTORY_SEPARATOR . $fileName)) {
            throw new ImagesException('Cannot find that file.');
        }

        if (is_file($targetPath . DIRECTORY_SEPARATOR . $fileName) && !$overwrite) {
            throw new ImagesException('File with the same name already exists here.');
        }

        $this->overwriteDataRename(
            $sourcePath . DIRECTORY_SEPARATOR . $fileName,
            $targetPath . DIRECTORY_SEPARATOR . $fileName,
            'Cannot remove old image.',
            'Cannot move base image.'
        );

        $this->overwriteDataRename(
            $sourcePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt(),
            $targetPath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt(),
            'Cannot remove old description.',
            'Cannot move description.'
        );

        $this->overwriteDataRename(
            $sourcePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir() . DIRECTORY_SEPARATOR . $fileName,
            $targetPath . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir() . DIRECTORY_SEPARATOR . $fileName,
            'Cannot remove old thumb.',
            'Cannot move thumb.'
        );
        return true;
    }

    /**
     * @param string $path
     * @param string $targetName
     * @param bool $overwrite
     * @return bool
     * @throws ExtrasException
     * @throws ImagesException
     */
    public function rename(string $path, string $targetName, bool $overwrite = false): bool
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);

        $whatPath = $this->libExtendDir->getWebRootDir() . $filePath;

        $this->checkWritable($whatPath);

        if (!is_file($whatPath . DIRECTORY_SEPARATOR . $fileName)) {
            throw new ImagesException('Cannot find that file.');
        }

        if (is_file($whatPath . DIRECTORY_SEPARATOR . $targetName) && !$overwrite) {
            throw new ImagesException('File with the same name already exists here.');
        }

        $this->overwriteDataRename(
            $whatPath . DIRECTORY_SEPARATOR . $fileName,
            $whatPath . DIRECTORY_SEPARATOR . $targetName,
            'Cannot remove old image.',
            'Cannot rename base image.'
        );

        $this->overwriteDataRename(
            $whatPath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt(),
            $whatPath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $targetName . $this->libExtendDir->getDescExt(),
            'Cannot remove old description.',
            'Cannot rename description.'
        );

        $this->overwriteDataRename(
            $whatPath . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir() . DIRECTORY_SEPARATOR . $fileName,
            $whatPath . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir() . DIRECTORY_SEPARATOR . $targetName,
            'Cannot remove old thumb.',
            'Cannot rename thumb.'
        );
        return true;
    }

    /**
     * @param string $path
     * @return bool
     * @throws ExtrasException
     * @throws ImagesException
     */
    public function delete(string $path): bool
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);

        $whatPath = $this->libExtendDir->getWebRootDir() . $filePath;

        $this->checkWritable($whatPath);

        if (!is_file($whatPath . DIRECTORY_SEPARATOR . $fileName)) {
            return true;
        }

        $this->unlinkMore(
            $whatPath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt(),
            'Cannot remove description!'
        );

        $this->unlinkMore(
            $whatPath . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir() . DIRECTORY_SEPARATOR . $fileName,
            'Cannot remove thumb!'
        );

        $this->unlinkMore(
            $whatPath . DIRECTORY_SEPARATOR . $fileName,
            'Cannot remove file!'
        );

        return true;
    }

    /**
     * @param string $path
     * @throws ExtrasException
     */
    protected function checkWritable(string $path): void
    {
        $this->libExtendDir->isWritable($path);
        $this->libExtendDir->isWritable($path . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir());
        $this->libExtendDir->isWritable($path . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir());
    }

    /**
     * @param string $source
     * @param string $target
     * @param string $unlinkErrDesc
     * @param string $copyErrDesc
     * @throws ImagesException
     */
    protected function overwriteDataCopy(string $source, string $target, string $unlinkErrDesc, string $copyErrDesc): void
    {
        if (is_file($target) && !unlink($target)) {
            throw new ImagesException($unlinkErrDesc);
        }
        if (is_file($source) && !copy($source, $target)) {
            throw new ImagesException($copyErrDesc);
        }
    }

    /**
     * @param string $source
     * @param string $target
     * @param string $unlinkErrDesc
     * @param string $copyErrDesc
     * @throws ImagesException
     */
    protected function overwriteDataRename(string $source, string $target, string $unlinkErrDesc, string $copyErrDesc): void
    {
        if (is_file($target) && !unlink($target)) {
            throw new ImagesException($unlinkErrDesc);
        }
        if (is_file($source) && !rename($source, $target)) {
            throw new ImagesException($copyErrDesc);
        }
    }

    /**
     * @param string $source
     * @param string $unlinkErrDesc
     * @throws ImagesException
     */
    protected function unlinkMore(string $source, string $unlinkErrDesc): void
    {
        if (is_file($source) && !unlink($source)) {
            throw new ImagesException($unlinkErrDesc);
        }
    }
}
