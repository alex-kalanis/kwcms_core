<?php

namespace kalanis\kw_files\Extended;


/**
 * Class Config
 * Work with extended dirs - which values are which
 */
class Config
{
    protected const FILE_TEMP = '.tmp';
    protected const FILE_EXT = '.png';

    protected string $descDir = '.txt'; # description dir
    protected string $descFile = 'index'; # description index filename
    protected string $descExt = '.dsc'; # description file's extension - add to original name
    protected string $thumbDir = '.tmb'; # thumbnail dir
    protected string $thumbExt = self::FILE_EXT;
    protected string $thumbTemp = self::FILE_TEMP;

    /**
     * @param array<string, string|int> $params
     * @return $this
     */
    public function setData(array $params = []): self
    {
        $this->descDir = !empty($params['desc_dir']) ? strval($params['desc_dir']) : $this->descDir;
        $this->descFile = !empty($params['desc_file']) ? strval($params['desc_file']) : $this->descFile;
        $this->descExt = !empty($params['desc_ext']) ? strval($params['desc_ext']) : $this->descExt;
        $this->thumbDir = !empty($params['thumb_dir']) ? strval($params['thumb_dir']) : $this->thumbDir;
        $this->thumbExt = !empty($params['tmb_ext']) ? strval($params['tmb_ext']) : $this->thumbExt;
        $this->thumbTemp = !empty($params['tmb_temp']) ? strval($params['tmb_temp']) : $this->thumbTemp;
        return $this;
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

    public function getThumbExt(): string
    {
        return $this->thumbExt;
    }

    public function getThumbTemp(): string
    {
        return $this->thumbTemp;
    }
}
