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

    /**
     * @param array<string, string|int> $params
     * @return $this
     */
    public function setData(array $params = []): self
    {
        $this->descDir = !is_null($params['desc_dir']) ? strval($params['desc_dir']) : $this->descDir;
        $this->descFile = !is_null($params['desc_file']) ? strval($params['desc_file']) : $this->descFile;
        $this->descExt = !is_null($params['desc_ext']) ? strval($params['desc_ext']) : $this->descExt;
        $this->thumbDir = !is_null($params['thumb_dir']) ? strval($params['thumb_dir']) : $this->thumbDir;
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
}
