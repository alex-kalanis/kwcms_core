<?php

namespace KWCMS\modules\Images\Lib;


use Error;
use kalanis\kw_extras\ExtrasException;
use kalanis\kw_extras\TNameFinder;
use kalanis\kw_images\Files;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Interfaces\IFileEntry;
use KWCMS\modules\Images\Interfaces\IProcessFiles;


/**
 * Class ProcessFile
 * @package KWCMS\modules\Images\Lib
 * Process files in many ways
 * @todo: vrazit jako motor KW_STORAGE - pak patricne zmizi cast chlivu souvisejici s obsluhou disku
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

    public function uploadFile(IFileEntry $file, string $description): bool
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
        if (!empty($description)) {
            $this->libFiles->getLibDesc()->set($this->sourcePath . $useName, $description);
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

    public function readDesc(string $path): string
    {
        return $this->libFiles->getLibDesc()->get($path);
    }

    public function updateDesc(string $path, string $content): void
    {
        if (empty($content)) {
            $this->libFiles->getLibDesc()->delete($path, '');
        } else {
            $this->libFiles->getLibDesc()->set($path, $content);
        }
    }

    public function copyFile(string $currentPath, string $toPath, bool $overwrite = false): bool
    {
        return $this->libFiles->copy($currentPath, $toPath, $overwrite);
    }

    public function moveFile(string $currentPath, string $toPath, bool $overwrite = false): bool
    {
        return $this->libFiles->move($currentPath, $toPath, $overwrite);
    }

    public function renameFile(string $currentPath, string $toFileName, bool $overwrite = false): bool
    {
        return $this->libFiles->rename($currentPath, $toFileName, $overwrite);
    }

    public function deleteFile(string $path): bool
    {
        return $this->libFiles->delete($path);
    }
}
