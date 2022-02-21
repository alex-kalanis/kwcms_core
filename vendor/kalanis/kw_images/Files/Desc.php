<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_images\ImagesException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_paths\PathsException;


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
        $realOne = realpath($whatPath);
        if ((false === $realOne) || !is_readable($whatPath)) {
            return '';
        }
        $content = file_get_contents($whatPath);
        if (false === $content) {
            throw new ImagesException($this->getLang()->imDescCannotRead());
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
            throw new ImagesException($this->getLang()->imDescCannotAdd());
        }
    }

    /**
     * @param string $fileName
     * @param string $sourceDir
     * @param string $targetDir
     * @param bool $overwrite
     * @return bool
     * @throws ImagesException
     * @throws PathsException
     */
    public function copy(string $fileName, string $sourceDir, string $targetDir, bool $overwrite = false): bool
    {
        $sourcePath = $this->libExtendDir->getWebRootDir() . $sourceDir . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir();
        $targetPath = $this->libExtendDir->getWebRootDir() . $targetDir . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir();

        $this->checkWritable($targetPath);
        $this->dataCopy(
            $sourcePath . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt(),
            $targetPath . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt(),
            $overwrite,
            $this->getLang()->imDescCannotFind(),
            $this->getLang()->imDescAlreadyExistsHere(),
            $this->getLang()->imDescCannotRemoveOld(),
            $this->getLang()->imDescCannotCopyBase()
        );

        return true;
    }

    /**
     * @param string $fileName
     * @param string $sourceDir
     * @param string $targetDir
     * @param bool $overwrite
     * @throws ImagesException
     * @throws PathsException
     */
    public function move(string $fileName, string $sourceDir, string $targetDir, bool $overwrite = false): void
    {
        $sourcePath = $this->libExtendDir->getWebRootDir() . $sourceDir . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir();
        $targetPath = $this->libExtendDir->getWebRootDir() . $targetDir . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir();

        $this->checkWritable($targetPath);
        $this->dataRename(
            $sourcePath . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt(),
            $targetPath . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt(),
            $overwrite,
            $this->getLang()->imDescCannotFind(),
            $this->getLang()->imDescAlreadyExistsHere(),
            $this->getLang()->imDescCannotRemoveOld(),
            $this->getLang()->imDescCannotMoveBase()
        );
    }

    /**
     * @param string $path
     * @param string $sourceName
     * @param string $targetName
     * @param bool $overwrite
     * @throws ImagesException
     * @throws PathsException
     */
    public function rename(string $path, string $sourceName, string $targetName, bool $overwrite = false): void
    {
        $whatPath = $this->libExtendDir->getWebRootDir() . $path. DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir();

        $this->checkWritable($whatPath);
        $this->dataRename(
            $whatPath . DIRECTORY_SEPARATOR . $sourceName . $this->libExtendDir->getDescExt(),
            $whatPath . DIRECTORY_SEPARATOR . $targetName . $this->libExtendDir->getDescExt(),
            $overwrite,
            $this->getLang()->imDescCannotFind(),
            $this->getLang()->imDescAlreadyExistsHere(),
            $this->getLang()->imDescCannotRemoveOld(),
            $this->getLang()->imDescCannotRenameBase()
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
        $this->dataRemove($whatPath, $this->getLang()->imDescCannotRemove());
    }

    public function getPath(string $path): string
    {
        $filePath = Stuff::removeEndingSlash(Stuff::directory($path));
        $fileName = Stuff::filename($path);
        return $filePath . DIRECTORY_SEPARATOR . $this->libExtendDir->getDescDir() . DIRECTORY_SEPARATOR . $fileName . $this->libExtendDir->getDescExt();
    }
}
