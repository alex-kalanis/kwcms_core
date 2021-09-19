<?php

namespace kalanis\kw_extras;


use kalanis\kw_paths\Stuff;


/**
 * Class ExtendDir
 * low-level work with extended dirs - which contains other params than just files and sub dirs
 */
class ExtendDir
{
    protected $webRootDir = ''; # system path to web root dir
    protected $descDir = '.txt'; # description dir
    protected $descFile = 'index'; # description index filename
    protected $descExt = '.dsc'; # description file's extension - add to original name
    protected $thumbDir = '.tmb'; # thumbnail dir

    public function __construct(string $webRootDir, ?string $descDir = null, ?string $descFile = null, ?string $descExt = null, ?string $thumbDir = null)
    {
        $this->webRootDir = $webRootDir;
        $this->descDir = $descDir ?: $this->descDir;
        $this->descFile = $descFile ?: $this->descFile;
        $this->descExt = $descExt ?: $this->descExt;
        $this->thumbDir = $thumbDir ?: $this->thumbDir;
    }

    public function getWebRootDir(): string
    {
        return $this->webRootDir;
    }

    /**
     * Make dir with extended properties
     * @param string $path
     * @return bool
     * @throws ExtrasException
     */
    public function makeExtended(string $path): bool
    {
        $current = Stuff::removeEndingSlash($path) . DIRECTORY_SEPARATOR;
        $descDir = $this->webRootDir . $current . $this->descDir;
        $thumbDir = $this->webRootDir . $current . $this->thumbDir;
        if (is_dir($descDir) && is_dir($thumbDir)) { // already exists
            return true;
        }
        if ((!@mkdir($descDir)) && (!is_dir($descDir))) {
            throw new ExtrasException('DESC_DIR_CANNOT_CREATE');
        }
        if ((!@mkdir($thumbDir)) && (!is_dir($thumbDir))) {
            throw new ExtrasException('THUMB_DIR_CANNOT_CREATE');
        }
        return true;
    }

    /**
     * @param string $path
     * @return bool
     * @throws ExtrasException
     */
    public function removeExtended(string $path): bool
    {
        $current = Stuff::removeEndingSlash($path) . DIRECTORY_SEPARATOR;
        $descDir = $this->webRootDir . $current . $this->descDir;
        $thumbDir = $this->webRootDir . $current . $this->thumbDir;

        $this->isWritable($descDir);
        $this->isWritable($thumbDir);
        $this->removeCycle($descDir);
        $this->removeCycle($thumbDir);
        return true;
    }

    /**
     * @param string $path
     * @param string $content
     * @return bool
     * @throws ExtrasException
     */
    public function setDirDescription(string $path, string $content): bool
    {
        $current = Stuff::removeEndingSlash($path) . DIRECTORY_SEPARATOR;
        $descDir = $this->webRootDir . $current . $this->descDir;
        $descFile = $this->descFile . $this->descExt;

        $descPath = null;
        if (!is_null($this->isUsable($descDir, $descFile))) {
            $descPath = $descDir . DIRECTORY_SEPARATOR . $descFile;
        }

        if (!is_null($descPath))  {
            if (false === file_put_contents($descPath, $content)) {
                throw new ExtrasException('DIR_DESC_NOT_WRITE');
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $path
     * @return string
     * @throws ExtrasException
     */
    public function getDirDescription(string $path): string
    {
        $current = Stuff::removeEndingSlash($path) . DIRECTORY_SEPARATOR;
        $descDir = $this->webRootDir . $current . $this->descDir;
        $descFile = $this->descFile . $this->descExt;

        $descPath = null;
        if (!is_null($this->isUsable($descDir, $descFile))) {
            $descPath = $descDir . DIRECTORY_SEPARATOR . $descFile;
        }

        $content = file_get_contents($descPath);
        if (false === $content) {
            throw new ExtrasException('DIR_DESC_NOT_READ');
        }
        return $content;
    }

    /**
     * Remove sub dirs and their content recursively
     * SHALL NOT BE SEPARATED INTO EXTRA CLASS
     * @param $dirPath
     */
    protected function removeCycle(string $dirPath): void
    {
        $path = Stuff::removeEndingSlash($dirPath);
        foreach (scandir($path) as $fileName) {
            if (is_dir($path . DIRECTORY_SEPARATOR . $fileName)) {
                if (($fileName != '.') || ($fileName != '..')) {
                    $this->removeCycle($path . DIRECTORY_SEPARATOR . $fileName);
                    rmdir($path . DIRECTORY_SEPARATOR . $fileName);
                }
            } else {
                unlink($path . DIRECTORY_SEPARATOR . $fileName);
            }
        }
    }

    /**
     * @param string $path
     * @return bool
     * @throws ExtrasException
     */
    protected function isReadable(string $path): bool
    {
        if (is_dir($path) && is_readable($path)) {
            return true;
        }
        throw new ExtrasException('CANNOT_ACCESS_DIR_READ');
    }

    /**
     * @param string $path
     * @return bool
     * @throws ExtrasException
     */
    protected function isWritable(string $path): bool
    {
        if (is_dir($path) && is_writable($path)) {
            return true;
        }
        throw new ExtrasException('CANNOT_ACCESS_DIR_WRITE');
    }

    /**
     * @param string $path
     * @param string $file
     * @return bool|null
     * @throws ExtrasException
     */
    protected function isUsable(string $path, string $file): ?bool
    {
        if (is_readable($path . DIRECTORY_SEPARATOR . $file) && is_writable($path . DIRECTORY_SEPARATOR . $file)) {
            return true;
        }
        if (!file_exists($path . DIRECTORY_SEPARATOR . $file) && is_readable($path) && is_writable($path)) {
            return null;
        }
        throw new ExtrasException('CANNOT_ACCESS_FILE');
    }
}
