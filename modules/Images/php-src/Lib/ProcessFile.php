<?php

namespace KWCMS\modules\Images\Lib;


use Error;
use kalanis\kw_extras\TNameFinder;
use kalanis\kw_images\Files;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Interfaces\IFileEntry;
use KWCMS\modules\Images\Interfaces\IProcessFiles;


/**
 * Class ProcessFile
 * @package KWCMS\modules\Images\Lib
 * Process files in many ways
 */
class ProcessFile implements IProcessFiles
{
    use TNameFinder;

    protected $libFiles = null;
    protected $sourcePath = '';

    public function __construct(Files $libFiles, string $sourcePath)
    {
        $this->libFiles = $libFiles;
        $this->sourcePath = $sourcePath;
    }

    public function uploadFile(IFileEntry $file, string $desc): bool
    {
        $useName = $this->findFreeName($file->getValue());
        try {
            $status = move_uploaded_file($file->getTempName(), $this->libFiles->getLibDirDesc()->getExtendDir()->getWebRootDir() . $this->sourcePath . $useName);
            if (!$status) {
                throw new ImagesException('Cannot move uploaded file');
            }
        } catch (Error $ex) {
            throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
        }
        $this->libFiles->getLibImage()->processUploaded($this->sourcePath . $useName);
        $this->libFiles->getLibThumb()->create($this->sourcePath . $useName);
        if (!empty($desc)) {
            $this->libFiles->getLibDesc()->set($this->sourcePath . $useName, $desc);
        }
        return true;
    }

    protected function getSeparator(): string
    {
        return static::FREE_NAME_SEPARATOR;
    }

    protected function getTargetDir(): string
    {
        return $this->sourcePath;
    }

    protected function targetExists(string $path): bool
    {
        return file_exists($path);
    }

    public function readDesc(string $entry): string
    {
        return $this->libFiles->getLibDesc()->get($entry);
    }

    public function updateDesc(string $entry, string $content): void
    {
        if (empty($content)) {
            $this->libFiles->getLibDesc()->delete($entry);
        } else {
            $this->libFiles->getLibDesc()->set($entry, $content);
        }
    }

    public function copyFile(string $entry, string $to, bool $overwrite = false): bool
    {
        $this->libFiles->getLibImage()->copy($entry, $to, $overwrite);
        $this->libFiles->getLibThumb()->copy($entry, $to, $overwrite);
        $this->libFiles->getLibDesc()->copy($entry, $to, $overwrite);
        return true;
    }

    public function moveFile(string $entry, string $to, bool $overwrite = false): bool
    {
        $this->libFiles->getLibImage()->move($entry, $to, $overwrite);
        $this->libFiles->getLibThumb()->move($entry, $to, $overwrite);
        $this->libFiles->getLibDesc()->move($entry, $to, $overwrite);
        return true;
    }

    public function renameFile(string $entry, string $to, bool $overwrite = false): bool
    {
        $this->libFiles->getLibImage()->rename($entry, $to, $overwrite);
        $this->libFiles->getLibThumb()->rename($entry, $to, $overwrite);
        $this->libFiles->getLibDesc()->rename($entry, $to, $overwrite);
        return true;
    }

    public function deleteFile(string $entry): bool
    {
        $this->libFiles->getLibDesc()->delete($entry);
        $this->libFiles->getLibThumb()->delete($entry);
        $this->libFiles->getLibImage()->delete($entry);
        return true;
    }
}
