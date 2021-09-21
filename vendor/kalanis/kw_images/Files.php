<?php

namespace kalanis\kw_images;


use kalanis\kw_extras\ExtendDir;
use kalanis\kw_extras\ExtrasException;
use kalanis\kw_paths\Stuff;


/**
 * Class Files
 * @package kalanis\kw_images
 *
 * ucel aktualni hry je odsekat ty casti, ktere reprezentuji samotny obrazek a vyhodit je do samostatne tridy - stejne jako byly vyhozeny thumby a popisky
 * Tady zustanou jen hromadna volani operaci nad celou sbirkou souboru
 */
class Files extends Files\AFiles
{
    protected $imageMaxWidth = 1024;
    protected $imageMaxHeight = 1024;
    protected $imageMaxFileSize = 1024;
    protected $libThumb = null;
    protected $libDesc = null;

    public function __construct(ExtendDir $libExtendDir, Files\Thumb $thumb, Files\Desc $desc, array $params = [])
    {
        parent::__construct($libExtendDir);
        $this->libThumb = $thumb;
        $this->libDesc = $desc;
        $this->imageMaxWidth = !empty($params["max_width"]) ? strval($params["max_width"]) : $this->imageMaxWidth;
        $this->imageMaxHeight = !empty($params["max_height"]) ? strval($params["max_height"]) : $this->imageMaxHeight;
        $this->imageMaxFileSize = !empty($params["max_size"]) ? strval($params["max_size"]) : $this->imageMaxFileSize;
    }

    public function getImageCreated(string $path, string $format = 'd.m.Y \@ H:i:s'): ?string
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
     * @param string $desc
     * @param bool $hasThumb
     * @return bool
     * @throws ImagesException
     */
    public function add(string $path, string $desc = '', bool $hasThumb = true): bool
    {
        $this->libThumb->remove($path);
        if ($hasThumb) {
            $this->libThumb->create($path);
        }

        if (!empty($desc)) {
            $this->libDesc->set($path, $desc);
        } else {
            $this->libDesc->remove($path);
        }

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
