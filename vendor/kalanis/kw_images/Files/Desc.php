<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_extras\ExtrasException;
use kalanis\kw_images\ImagesException;
use kalanis\kw_paths\Stuff;


/**
 * Class Desc
 * Content description
 * @package kalanis\kw_images\Files
 */
class Desc extends AFiles
{
    /**
     * @param string $path
     * @return string
     * @throws ImagesException
     */
    public function get(string $path): string
    {
        $whatPath = $this->libExtendDir->getWebRootDir() . $this->getPath($path);
        if (!is_file($whatPath) || !is_readable($whatPath)) {
            return '';
        }
        $content = file_get_contents($whatPath);
        if (false === $content) {
            throw new ImagesException('Cannot read description');
        }
        return $content;
    }

    /**
     * @param string $path
     * @param string $content
     * @throws ImagesException
     */
    public function set(string $path, string $content): void
    {
        $whatPath = $this->libExtendDir->getWebRootDir() . $this->getPath($path);

        if (false === file_put_contents( $whatPath, $content )) {
            throw new ImagesException('Cannot add description');
        }
    }

    /**
     * @param string $fileName
     * @param string $sourceDir
     * @param string $targetDir
     * @param bool $overwrite
     * @return bool
     * @throws ExtrasException
     * @throws ImagesException
     */
    public function copy(string $fileName, string $sourceDir, string $targetDir, bool $overwrite = false): bool
    {
        $sourcePath = $this->libExtendDir->getWebRootDir() . $sourceDir . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir();
        $targetPath = $this->libExtendDir->getWebRootDir() . $targetDir . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir();

        $this->checkWritable($targetPath);
        $this->dataCopy(
            $sourcePath . DIRECTORY_SEPARATOR . $fileName,
            $targetPath . DIRECTORY_SEPARATOR . $fileName,
            $overwrite,
            'Cannot find that description.',
            'Description with the same name already exists here.',
            'Cannot remove old description.',
            'Cannot copy base description.'
        );

        return true;
    }

    /**
     * @param string $fileName
     * @param string $sourceDir
     * @param string $targetDir
     * @param bool $overwrite
     * @throws ExtrasException
     * @throws ImagesException
     */
    public function move(string $fileName, string $sourceDir, string $targetDir, bool $overwrite = false): void
    {
        $sourcePath = $this->libExtendDir->getWebRootDir() . $sourceDir . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir();
        $targetPath = $this->libExtendDir->getWebRootDir() . $targetDir . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir();

        $this->checkWritable($targetPath);
        $this->dataRename(
            $sourcePath . DIRECTORY_SEPARATOR . $fileName,
            $targetPath . DIRECTORY_SEPARATOR . $fileName,
            $overwrite,
            'Cannot find that description.',
            'Description with the same name already exists here.',
            'Cannot remove old description.',
            'Cannot move base description.'
        );
    }

    /**
     * @param string $path
     * @param string $sourceName
     * @param string $targetName
     * @param bool $overwrite
     * @throws ExtrasException
     * @throws ImagesException
     */
    public function rename(string $path, string $sourceName, string $targetName, bool $overwrite = false): void
    {
        $whatPath = $this->libExtendDir->getWebRootDir() . $path. DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir();

        $this->checkWritable($whatPath);
        $this->dataRename(
            $whatPath . DIRECTORY_SEPARATOR . $sourceName,
            $whatPath . DIRECTORY_SEPARATOR . $targetName,
            $overwrite,
            'Cannot find that description.',
            'Description the same name already exists here.',
            'Cannot remove old description.',
            'Cannot rename base description.'
        );
    }

    /**
     * @param string $sourceDir
     * @param string $fileName
     * @throws ImagesException
     */
    public function delete(string $sourceDir, string $fileName): void
    {
        $this->deleteFile($this->getPath($sourceDir . DIRECTORY_SEPARATOR . $fileName), 'Cannot remove description!');
    }

    public function getPath(string $path): string
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);
        return $filePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt();
    }
}
