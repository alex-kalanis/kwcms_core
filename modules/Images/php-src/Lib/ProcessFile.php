<?php

namespace KWCMS\modules\Images\Lib;


use Error;
use kalanis\kw_images\Files;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Interfaces\IFileEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_paths\Extras\TNameFinder;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Images\Interfaces\IProcessFiles;


/**
 * Class ProcessFile
 * @package KWCMS\modules\Images\Lib
 * Process files in many ways
 * @todo: use KW_FILES as data source - that will remove that part with volume service
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

    public function uploadFile(IFileEntry $file, string $targetName, string $description): bool
    {
        $targetPath = $this->sourcePath . $targetName;
        try {
            $status = move_uploaded_file($file->getTempName(), $this->libFiles->getLibImage()->getExtendDir()->getWebRootDir() . $targetPath);
            if (!$status) {
                throw new ImagesException(Lang::get('images.upload.cannot_move'));
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
        return $this->libFiles->getLibImage()->getExtendDir()->getWebRootDir() . $this->sourcePath;
    }

    protected function targetExists(string $path): bool
    {
        return file_exists(Stuff::sanitize($path));
    }

    public function readCreated(string $path, string $format = 'Y-m-d H:i:s'): string
    {
        return $this->libFiles->getLibImage()->getCreated(Stuff::sanitize($path), $format) ?: '';
    }

    public function readDesc(string $path): string
    {
        return $this->libFiles->getLibDesc()->get(Stuff::sanitize($path));
    }

    public function updateDesc(string $path, string $content): void
    {
        if (empty($content)) {
            $path = Stuff::sanitize($path);
            $origDir = Stuff::removeEndingSlash(Stuff::directory($path));
            $fileName = Stuff::filename($path);
            $this->libFiles->getLibDesc()->delete($origDir, $fileName);
        } else {
            $this->libFiles->getLibDesc()->set(Stuff::sanitize($path), $content);
        }
    }

    public function copyFile(string $currentPath, string $toPath, bool $overwrite = false): bool
    {
        return $this->libFiles->copy(Stuff::sanitize($currentPath), Stuff::sanitize($toPath), $overwrite);
    }

    /**
     * @param string $currentPath
     * @param string $toPath
     * @param bool $overwrite
     * @return bool
     * @throws ImagesException
     */
    public function moveFile(string $currentPath, string $toPath, bool $overwrite = false): bool
    {
        return $this->libFiles->move(Stuff::sanitize($currentPath), Stuff::sanitize($toPath), $overwrite);
    }

    /**
     * @param string $currentPath
     * @param string $toFileName
     * @param bool $overwrite
     * @return bool
     * @throws ImagesException
     */
    public function renameFile(string $currentPath, string $toFileName, bool $overwrite = false): bool
    {
        return $this->libFiles->rename(Stuff::sanitize($currentPath), $toFileName, $overwrite);
    }

    public function deleteFile(string $path): bool
    {
        return $this->libFiles->delete(Stuff::sanitize($path));
    }

    public function getLibFiles(): Files
    {
        return $this->libFiles;
    }
}
