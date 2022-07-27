<?php

namespace kalanis\kw_files\Extended;


/**
 * Class Config
 * Work with extended dirs - which values are which
 */
class Config
{
    /** @var string */
    protected $descDir = '.txt'; # description dir
    /** @var string */
    protected $descFile = 'index'; # description index filename
    /** @var string */
    protected $descExt = '.dsc'; # description file's extension - add to original name
    /** @var string */
    protected $thumbDir = '.tmb'; # thumbnail dir

    public function __construct(?string $descDir = null, ?string $descFile = null, ?string $descExt = null, ?string $thumbDir = null)
    {
        $this->descDir = $descDir ?: $this->descDir;
        $this->descFile = $descFile ?: $this->descFile;
        $this->descExt = $descExt ?: $this->descExt;
        $this->thumbDir = $thumbDir ?: $this->thumbDir;
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
}
