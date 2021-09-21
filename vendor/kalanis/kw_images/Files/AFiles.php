<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_extras\ExtendDir;
use kalanis\kw_extras\ExtrasException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_paths\Stuff;


/**
 * Class AFiles
 * @package kalanis\kw_images\Files
 */
abstract class AFiles
{
    /** @var ExtendDir|null */
    protected $libExtendDir = null;

    public function __construct(ExtendDir $libExtendDir)
    {
        $this->libExtendDir = $libExtendDir;
    }

    abstract public function getPath(string $path): string;

    /**
     * @param string $filePath
     * @param string $fileName
     * @param string $targetDir
     * @param bool $overwrite
     * @return bool
     * @throws ImagesException
     */
    protected function copyFile(string $filePath, string $fileName, string $targetDir, bool $overwrite = false): bool
    {
        $sourcePath = $this->libExtendDir->getWebRootDir() . $filePath;
        $targetPath = $this->libExtendDir->getWebRootDir() . Stuff::removeEndingSlash($targetDir);

        if (!is_file($sourcePath . DIRECTORY_SEPARATOR . $fileName)) {
            throw new ImagesException('Cannot find that file.');
        }

        if (is_file($targetPath . DIRECTORY_SEPARATOR . $fileName) && !$overwrite) {
            throw new ImagesException('File with the same name already exists here.');
        }

        $this->dataOverwriteCopy(
            $sourcePath . DIRECTORY_SEPARATOR . $fileName,
            $targetPath . DIRECTORY_SEPARATOR . $fileName,
            'Cannot remove old file.',
            'Cannot copy base file.'
        );
        return true;
    }

    /**
     * @param string $filePath
     * @param string $fileName
     * @param string $targetDir
     * @param bool $overwrite
     * @return bool
     * @throws ImagesException
     */
    protected function moveFile(string $filePath, string $fileName, string $targetDir, bool $overwrite = false): bool
    {
        $sourcePath = $this->libExtendDir->getWebRootDir() . $filePath;
        $targetPath = $this->libExtendDir->getWebRootDir() . Stuff::removeEndingSlash($targetDir);

        if (!is_file($sourcePath . DIRECTORY_SEPARATOR . $fileName)) {
            throw new ImagesException('Cannot find that file.');
        }

        if (is_file($targetPath . DIRECTORY_SEPARATOR . $fileName) && !$overwrite) {
            throw new ImagesException('File with the same name already exists here.');
        }

        $this->dataOverwriteRename(
            $sourcePath . DIRECTORY_SEPARATOR . $fileName,
            $targetPath . DIRECTORY_SEPARATOR . $fileName,
            'Cannot remove old file.',
            'Cannot move base file.'
        );
        return true;
    }

    /**
     * @param string $filePath
     * @param string $fileName
     * @param string $targetName
     * @param bool $overwrite
     * @return bool
     * @throws ImagesException
     */
    protected function renameFile(string $filePath, string $fileName, string $targetName, bool $overwrite = false): bool
    {
        $whatPath = $this->libExtendDir->getWebRootDir() . $filePath;

        if (!is_file($whatPath . DIRECTORY_SEPARATOR . $fileName)) {
            throw new ImagesException('Cannot find that file.');
        }

        if (is_file($whatPath . DIRECTORY_SEPARATOR . $targetName) && !$overwrite) {
            throw new ImagesException('File with the same name already exists here.');
        }

        $this->dataOverwriteRename(
            $whatPath . DIRECTORY_SEPARATOR . $fileName,
            $whatPath . DIRECTORY_SEPARATOR . $targetName,
            'Cannot remove old file.',
            'Cannot rename base file.'
        );
        return true;
    }

    /**
     * @param string $path
     * @param string $unlinkErrDesc
     * @return bool
     * @throws ImagesException
     */
    protected function deleteFile(string $path, string $unlinkErrDesc): bool
    {
        $whatPath = $this->libExtendDir->getWebRootDir() . $this->getPath($path);

        if (!is_file($whatPath)) {
            return true;
        }
        $this->dataRemove($whatPath, $unlinkErrDesc);
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
     * @param string $source
     * @param string $target
     * @param string $unlinkErrDesc
     * @param string $copyErrDesc
     * @throws ImagesException
     */
    protected function dataOverwriteCopy(string $source, string $target, string $unlinkErrDesc, string $copyErrDesc): void
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
    protected function dataOverwriteRename(string $source, string $target, string $unlinkErrDesc, string $copyErrDesc): void
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
    protected function dataRemove(string $source, string $unlinkErrDesc): void
    {
        if (is_file($source) && !unlink($source)) {
            throw new ImagesException($unlinkErrDesc);
        }
    }
}
