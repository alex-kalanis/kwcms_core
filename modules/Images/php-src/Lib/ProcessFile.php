<?php

namespace KWCMS\modules\Images\Lib;


use kalanis\kw_files\FilesException;
use kalanis\kw_files\Interfaces\IProcessDirs;
use kalanis\kw_files\Interfaces\IProcessFiles;
use kalanis\kw_files\Node;
use kalanis\kw_images\Files;
use kalanis\kw_images\ImagesException;
use kalanis\kw_input\Interfaces\IFileEntry;
use kalanis\kw_paths\Extras\TNameFinder;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Images\Interfaces\IProcessFiles as IProc;


/**
 * Class ProcessFile
 * @package KWCMS\modules\Images\Lib
 * Process files in many ways
 * @todo: use KW_FILES as data source - that will remove that part with volume service
 * @todo: use operations in kw_image/Content
 */
class ProcessFile implements IProc
{
    use TNameFinder;

    /** @var Files */
    protected $libFiles = null;
    protected $sourcePath = '';
    /** @var Node */
    protected $currentNode = null;
    /** @var IProcessDirs */
    protected $dirProcessor = null;
    /** @var IProcessFiles */
    protected $fileProcessor = null;

    public function __construct(Files $libFiles, string $sourcePath, IProcessFiles $fileProcessor)
    {
        $this->libFiles = $libFiles;
        $this->sourcePath = $sourcePath;
//        $this->currentNode = $currentNode;
//        $this->dirProcessor = $dirProcessor;
        $this->fileProcessor = $fileProcessor;
    }

    /**
     * @param string $name
     * @throws FilesException
     * @return string
     */
    public function findFreeName(string $name): string
    {
        $name = Stuff::canonize($name);
        $ext = Stuff::fileExt($name);
        if (0 < mb_strlen($ext)) {
            $ext = IPaths::SPLITTER_DOT . $ext;
        }
        $fileName = Stuff::fileBase($name);
        return $this->fileProcessor->findFreeName([$fileName], $ext);
    }

    /**
     * @param IFileEntry $file
     * @param string $targetName
     * @param string $description
     * @throws FilesException
     * @return bool
     */
    public function uploadFile(IFileEntry $file, string $targetName, string $description): bool
    {
//        $stream = fopen($file->getTempName(), 'rb+');
//        $path = $this->currentNode->getPath() + [Stuff::filename($targetName)];
//        return $this->fileProcessor->saveFile($path, $stream);

        $path = Stuff::pathToArray($this->sourcePath) + [Stuff::filename($targetName)];
        return $this->libFiles->add(null, $path, $file->getTempName(), $description);
    }

    protected function getSeparator(): string
    {
        return static::FREE_NAME_SEPARATOR;
    }

    protected function getTargetDir(): string
    {
        return $this->libFiles->getLibImage()->getProcessor()->getWebRootDir() . $this->sourcePath;
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
     * @throws ImagesException
     * @throws FilesException
     * @return bool
     */
    public function moveFile(string $currentPath, string $toPath, bool $overwrite = false): bool
    {
        return $this->libFiles->move(Stuff::sanitize($currentPath), Stuff::sanitize($toPath), $overwrite);
    }

    /**
     * @param string $currentPath
     * @param string $toFileName
     * @param bool $overwrite
     * @throws ImagesException
     * @throws FilesException
     * @return bool
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
