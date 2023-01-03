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
    /** @var Content\BasicOperations */
    protected $libOper = null;
    /** @var Content\ImageUpload */
    protected $libUpload = null;
    /** @var Content\Images */
    protected $libImages = null;
    /** @var string */
    protected $sourcePath = '';

    public function __construct(Content\BasicOperations $libOper, Content\ImageUpload $libUpload, Content\Images $libImages, string $sourcePath)
    {
        $this->libOper = $libOper;
        $this->libImages = $libImages;
        $this->libUpload = $libUpload;
        $this->sourcePath = $sourcePath;
    }

    public function findFreeName(string $name): string
    {
        return $this->libUpload->findFreeName(Stuff::linkToArray($this->sourcePath), $name);
    }

    public function uploadFile(IFileEntry $file, string $targetName, string $description): bool
    {
        $path = Stuff::linkToArray($this->sourcePath) + [Stuff::filename($targetName)];
        return $this->libUpload->process($path, $file->getTempName(), $description);
    }

    public function readCreated(string $path, string $format = 'Y-m-d H:i:s'): string
    {
        return $this->libImages->created(Stuff::linkToArray(Stuff::sanitize($path)), $format) ?: '';
    }

    public function readDesc(string $path): string
    {
        return $this->libImages->getDescription(Stuff::linkToArray(Stuff::sanitize($path)));
    }

    public function updateDesc(string $path, string $content): bool
    {
        return $this->libImages->updateDescription(Stuff::linkToArray(Stuff::sanitize($path)), $content);
    }

    public function updateThumb(string $path): bool
    {
        return $this->libImages->updateThumb(Stuff::linkToArray(Stuff::sanitize($path)));
    }

    public function copyFile(string $currentPath, string $toPath, bool $overwrite = false): bool
    {
        return $this->libOper->copy(Stuff::linkToArray(Stuff::sanitize($currentPath)), Stuff::linkToArray(Stuff::sanitize($toPath)), $overwrite);
    }

    public function moveFile(string $currentPath, string $toPath, bool $overwrite = false): bool
    {
        return $this->libOper->move(Stuff::linkToArray(Stuff::sanitize($currentPath)), Stuff::linkToArray(Stuff::sanitize($toPath)), $overwrite);
    }

    public function renameFile(string $currentPath, string $toFileName, bool $overwrite = false): bool
    {
        return $this->libOper->rename(Stuff::linkToArray(Stuff::sanitize($currentPath)), $toFileName, $overwrite);
    }

    public function deleteFile(string $path): bool
    {
        return $this->libOper->delete(Stuff::linkToArray(Stuff::sanitize($path)));
    }

    public function reverseImage(string $path): string
    {
        return Stuff::arrayToPath($this->libImages->reversePath(Stuff::linkToArray($path)));
    }

    public function reverseThumb(string $path): string
    {
        return Stuff::arrayToPath($this->libImages->reverseThumbPath(Stuff::linkToArray($path)));
    }

    public function getLibImage(): Content\Images
    {
        return $this->libImages;
    }
}
