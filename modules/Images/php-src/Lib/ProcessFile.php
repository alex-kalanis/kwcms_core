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
 * @todo: use KW_STORAGE as data source - that will remove that part with volume service
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
        $targetPath = $this->sourcePath . $this->findFreeName($file->getValue());
        try {
            $status = move_uploaded_file($file->getTempName(), $this->libFiles->getLibThumb()->getExtendDir()->getWebRootDir() . $targetPath);
            if (!$status) {
                throw new ImagesException('Cannot move uploaded file');
            }
        } catch (Error $ex) {
            throw new ImagesException($ex->getMessage(), $ex->getCode(), $ex);
        }
        return $this->libFiles->add($targetPath, $description);
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

    public function readCreated(string $path, string $format = 'Y-m-d H:i:s'): string
    {
        return $this->libFiles->getLibImage()->getCreated($path, $format) ?: '';
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

    /**
     * @param string $currentPath
     * @param string $toPath
     * @param bool $overwrite
     * @return bool
     * @throws ImagesException
     * @throws ExtrasException
     */
    public function moveFile(string $currentPath, string $toPath, bool $overwrite = false): bool
    {
        return $this->libFiles->move($currentPath, $toPath, $overwrite);
    }

    /**
     * @param string $currentPath
     * @param string $toFileName
     * @param bool $overwrite
     * @return bool
     * @throws ExtrasException
     * @throws ImagesException
     */
    public function renameFile(string $currentPath, string $toFileName, bool $overwrite = false): bool
    {
        return $this->libFiles->rename($currentPath, $toFileName, $overwrite);
    }

    public function deleteFile(string $path): bool
    {
        return $this->libFiles->delete($path);
    }
}
