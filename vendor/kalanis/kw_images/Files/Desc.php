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

        $sourcePath = $this->libExtendDir->getWebRootDir() . $filePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir();
        $targetPath = $this->libExtendDir->getWebRootDir() . Stuff::removeEndingSlash($targetDir) . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir();

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

        $sourcePath = $this->libExtendDir->getWebRootDir() . $filePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir();
        $targetPath = $this->libExtendDir->getWebRootDir() . Stuff::removeEndingSlash($targetDir) . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir();

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
     * @param string $targetName
     * @param bool $overwrite
     * @throws ExtrasException
     * @throws ImagesException
     */
    public function rename(string $path, string $targetName, bool $overwrite = false): void
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);

        $whatPath = $this->libExtendDir->getWebRootDir() . $filePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir();

        $this->checkWritable($whatPath);
        $this->dataRename(
            $whatPath . DIRECTORY_SEPARATOR . $fileName,
            $whatPath . DIRECTORY_SEPARATOR . $targetName,
            $overwrite,
            'Cannot find that description.',
            'Description the same name already exists here.',
            'Cannot remove old description.',
            'Cannot rename base description.'
        );
    }

    /**
     * @param string $path
     * @throws ImagesException
     */
    public function delete(string $path): void
    {
        $this->deleteFile($this->getPath($path), 'Cannot remove description!');
    }

    public function getPath(string $path): string
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);
        return $filePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt();
    }
}
