<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_extras\ExtendDir;
use kalanis\kw_extras\ExtrasException;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_paths\Stuff;


/**
 * Class Image
 * @package kalanis\kw_images\Files
 */
class Image extends AFiles
{
    protected $maxWidth = 1024;
    protected $maxHeight = 1024;
    protected $maxFileSize = 1024;
    protected $libGraphics = null;

    public function __construct(ExtendDir $libExtendDir, Graphics $libGraphics, array $params = [])
    {
        parent::__construct($libExtendDir);
        $this->libGraphics = $libGraphics;
        $this->maxWidth = !empty($params["max_width"]) ? strval($params["max_width"]) : $this->maxWidth;
        $this->maxHeight = !empty($params["max_height"]) ? strval($params["max_height"]) : $this->maxHeight;
        $this->maxFileSize = !empty($params["max_size"]) ? strval($params["max_size"]) : $this->maxFileSize;
    }

    public function getCreated(string $path, string $format = 'd.m.Y \@ H:i:s'): ?string
    {
        $created = filemtime($this->libExtendDir->getWebRootDir() . $path);
        return (false === $created) ? null : date($format, $created);
    }

    public function getPath(string $path): string
    {
        return $path; // no modifications need
    }

    /**
     * @param string $path
     * @return bool
     * @throws ImagesException
     */
    public function upload(string $path): bool
    {
        $this->libGraphics->load($path);
        $sizes = $this->calculateSize($this->libGraphics->width(), $this->maxWidth, $this->libGraphics->height(), $this->maxHeight);
        $this->libGraphics->resample($sizes['width'], $sizes['height']);
        $this->libGraphics->save($path);
        return true;
    }

    /**
     * @param string $path
     * @throws ImagesException
     */
    public function remove(string $path): void
    {
        $this->deleteFile($this->getPath($path), 'Cannot remove image!');
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

        $this->dataOverwriteCopy(
            $sourcePath . DIRECTORY_SEPARATOR . $fileName,
            $targetPath . DIRECTORY_SEPARATOR . $fileName,
            'Cannot remove old image.',
            'Cannot copy base image.'
        );

        $this->dataOverwriteCopy(
            $sourcePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt(),
            $targetPath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt(),
            'Cannot remove old description.',
            'Cannot copy description.'
        );

        $this->dataOverwriteCopy(
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

        $this->dataOverwriteRename(
            $sourcePath . DIRECTORY_SEPARATOR . $fileName,
            $targetPath . DIRECTORY_SEPARATOR . $fileName,
            'Cannot remove old image.',
            'Cannot move base image.'
        );

        $this->dataOverwriteRename(
            $sourcePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt(),
            $targetPath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt(),
            'Cannot remove old description.',
            'Cannot move description.'
        );

        $this->dataOverwriteRename(
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

        $this->dataOverwriteRename(
            $whatPath . DIRECTORY_SEPARATOR . $fileName,
            $whatPath . DIRECTORY_SEPARATOR . $targetName,
            'Cannot remove old image.',
            'Cannot rename base image.'
        );

        $this->dataOverwriteRename(
            $whatPath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt(),
            $whatPath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $targetName . $this->libExtendDir->getDescExt(),
            'Cannot remove old description.',
            'Cannot rename description.'
        );

        $this->dataOverwriteRename(
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

        $this->dataRemove(
            $whatPath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt(),
            'Cannot remove description!'
        );

        $this->dataRemove(
            $whatPath . DIRECTORY_SEPARATOR . $this->libExtendDir->getThumbDir() . DIRECTORY_SEPARATOR . $fileName,
            'Cannot remove thumb!'
        );

        $this->dataRemove(
            $whatPath . DIRECTORY_SEPARATOR . $fileName,
            'Cannot remove file!'
        );

        return true;
    }
}
