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

    public function getDescDir(): string
    {
        return $this->descDir;
    }

    public function getDescFile(): string
    {
        return $this->descFile;
    }

    public function getDescExt(): string
    {
        return $this->descExt;
    }

    public function getThumbDir(): string
    {
        return $this->thumbDir;
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
            throw new ExtrasException('Cannot create description dir');
        }
        if ((!@mkdir($thumbDir)) && (!is_dir($thumbDir))) {
            throw new ExtrasException('Cannot create thumbnail dir');
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
    public function isReadable(string $path): bool
    {
        if (is_dir($path) && is_readable($path)) {
            return true;
        }
        throw new ExtrasException('Cannot access wanted directory!');
    }

    /**
     * @param string $path
     * @return bool
     * @throws ExtrasException
     */
    public function isWritable(string $path): bool
    {
        if (is_dir($path) && is_writable($path)) {
            return true;
        }
        throw new ExtrasException('Cannot write into that directory!');
    }

    public function isFile(string $path): bool
    {
        return is_file($path);
    }

    public function isDir(string $path): bool
    {
        return is_dir($path);
    }
}
