<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_images\Content;
use kalanis\kw_input\Interfaces\IFileEntry;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Images\Interfaces\IProcessFiles;


/**
 * Class ProcessFile
 * @package KWCMS\modules\Images\Lib
 * Process files in many ways
 */
class ProcessFile implements IProcessFiles
{
    protected Content\BasicOperations $libOper;
    protected Content\ImageUpload $libUpload;
    protected Content\Images $libImages;
    /** @var string[] */
    protected array $userDir = [];
    /** @var string[] */
    protected array $currentDir = [];

    /**
     * @param Content\BasicOperations $libOper
     * @param Content\ImageUpload $libUpload
     * @param Content\Images $libImages
     * @param string[] $userDir where is user set with his account
     * @param string[] $currentDir where is user walking now
     */
    public function __construct(Content\BasicOperations $libOper, Content\ImageUpload $libUpload, Content\Images $libImages, array $userDir, array $currentDir)
    {
        $this->libOper = $libOper;
        $this->libImages = $libImages;
        $this->libUpload = $libUpload;
        $this->userDir = $userDir;
        $this->currentDir = $currentDir;
    }

    public function findFreeName(string $name): string
    {
        return $this->libUpload->findFreeName(array_merge($this->userDir, $this->currentDir), $name);
    }

    public function uploadFile(IFileEntry $file, string $targetName, string $description, bool $orientate): bool
    {
        return $this->libUpload->process(array_merge($this->userDir, $this->currentDir, [Stuff::filename($targetName)]), $file->getTempName(), $description, $orientate);
    }

    public function readCreated(string $path, string $format = 'Y-m-d H:i:s'): string
    {
        return $this->libImages->created(array_merge($this->userDir, $this->currentDir, [Stuff::filename($path)])) ?: '';
    }

    public function readDesc(string $path): string
    {
        return $this->libImages->getDescription(array_merge($this->userDir, $this->currentDir, [Stuff::filename($path)]));
    }

    public function updateDesc(string $path, string $content): bool
    {
        return $this->libImages->updateDescription(array_merge($this->userDir, $this->currentDir, [Stuff::filename($path)]), $content);
    }

    public function updateThumb(string $path): bool
    {
        return $this->libImages->updateThumb(array_merge($this->userDir, $this->currentDir, [Stuff::filename($path)]));
    }

    public function copyFile(string $currentPath, string $toPath, bool $overwrite = false): bool
    {
        return $this->libOper->copy(array_merge($this->userDir, $this->currentDir, [Stuff::filename($currentPath)]), Stuff::linkToArray(Stuff::sanitize($toPath)), $overwrite);
    }

    public function moveFile(string $currentPath, string $toPath, bool $overwrite = false): bool
    {
        return $this->libOper->move(array_merge($this->userDir, $this->currentDir, [Stuff::filename($currentPath)]), Stuff::linkToArray(Stuff::sanitize($toPath)), $overwrite);
    }

    public function renameFile(string $currentPath, string $toFileName, bool $overwrite = false): bool
    {
        return $this->libOper->rename(array_merge($this->userDir, $this->currentDir, [Stuff::filename($currentPath)]), $toFileName, $overwrite);
    }

    public function deleteFile(string $path): bool
    {
        return $this->libOper->delete(array_merge($this->userDir, $this->currentDir, [Stuff::filename($path)]));
    }

    public function reverseImage(string $path): string
    {
        return Stuff::arrayToLink($this->libImages->reversePath(array_merge($this->currentDir, [Stuff::filename($path)])));
    }

    public function reverseThumb(string $path): string
    {
        return Stuff::arrayToLink($this->libImages->reverseThumbPath(array_merge($this->currentDir, [Stuff::filename($path)])));
    }

    public function getLibImage(): Content\Images
    {
        return $this->libImages;
    }
}
