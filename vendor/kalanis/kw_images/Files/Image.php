<?php

namespace kalanis\kw_images\Files;


use kalanis\kw_files\Extended\Processor;
use kalanis\kw_files\FilesException;
use kalanis\kw_images\Graphics;
use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_paths\Stuff;


/**
 * Class Image
 * Main image itself
 * @package kalanis\kw_images\Files
 */
class Image extends AFiles
{
    /** @var Graphics\Processor */
    protected $libGraphics = null;

    public function __construct(Processor $libProcessor, Graphics\Processor $libGraphics, ?IIMTranslations $lang = null)
    {
        parent::__construct($libProcessor, $lang);
        $this->libGraphics = $libGraphics;
    }

    /**
     * @param string $path
     * @param string $format
     * @throws FilesException
     * @return string|null
     */
    public function getCreated(string $path, string $format = 'Y-m-d H:i:s'): ?string
    {
        $created = $this->libProcessor->getNodeProcessor()->created($this->getPath($path));
        return (is_null($created)) ? null : date($format, $created);
    }

    /**
     * @param string $path
     * @param string|resource $content
     * @throws FilesException
     * @return bool
     */
    public function save(string $path, $content): bool
    {
        $this->libProcessor->getFileProcessor()->saveFile($this->getPath($path), $content);
        return true;
    }

    /**
     * @param string $path
     * @throws FilesException
     * @return string|resource
     */
    public function load(string $path)
    {
        return $this->libProcessor->getFileProcessor()->readFile($this->getPath($path));
    }

    /**
     * @param string $path path to temp file
     * @throws ImagesException
     * @todo: out
     */
    public function check(string $path): void
    {
        $this->libGraphics->check($path);
    }

    /**
     * @param string $tempPath path to temp file
     * @param string $realName real file name for extension detection
     * @throws ImagesException
     * @return bool
     * @todo: out
     */
    public function resizeLocally(string $tempPath, string $realName): bool
    {
        $this->libGraphics->resize($tempPath, $realName);
        return true;
    }

    /**
     * @param string $fileName
     * @param string $sourceDir
     * @param string $targetDir
     * @param bool $overwrite
     * @throws FilesException
     */
    public function copy(string $fileName, string $sourceDir, string $targetDir, bool $overwrite = false): void
    {
        $this->dataCopy(
            Stuff::pathToArray($sourceDir) + [$fileName],
            Stuff::pathToArray($targetDir) + [$fileName],
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
     * @throws FilesException
     */
    public function move(string $fileName, string $sourceDir, string $targetDir, bool $overwrite = false): void
    {
        $this->dataRename(
            Stuff::pathToArray($sourceDir) + [$fileName],
            Stuff::pathToArray($targetDir) + [$fileName],
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
     * @throws FilesException
     */
    public function rename(string $path, string $sourceName, string $targetName, bool $overwrite = false): void
    {
        $this->dataRename(
            Stuff::pathToArray($path) + [$sourceName],
            Stuff::pathToArray($path) + [$targetName],
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
     * @throws FilesException
     */
    public function delete(string $sourceDir, string $fileName): void
    {
        $whatPath = $this->getPath($sourceDir . DIRECTORY_SEPARATOR . $fileName);
        $this->dataRemove($whatPath, $this->getLang()->imImageCannotRemove());
    }

    public function getPath(string $path): array
    {
        return Stuff::pathToArray($path);
    }
}
