<?php

namespace kalanis\kw_images\Graphics;


use kalanis\kw_images\Interfaces\ISizes;


/**
 * Class ThumbConfig
 * File thumbnail configuration
 * @package kalanis\kw_images\Graphics
 */
class ThumbConfig implements ISizes
{
    const FILE_TEMP = '.tmp';

    /** @var int */
    protected $maxWidth = 180;
    /** @var int */
    protected $maxHeight = 180;
    /** @var int */
    protected $maxFileSize = 10485760;
    /** @var string */
    protected $thumbTempExt = self::FILE_TEMP;
    /** @var string */
    protected $tempDir = '';

    /**
     * @param array<string, string|int> $params
     * @return $this
     */
    public function setData(array $params = []): self
    {
        $this->maxWidth = !empty($params['tmb_width']) ? intval(strval($params['tmb_width'])) : $this->maxWidth;
        $this->maxHeight = !empty($params['tmb_height']) ? intval(strval($params['tmb_height'])) : $this->maxHeight;
        $this->maxFileSize = !empty($params['tmb_size']) ? intval(strval($params['tmb_size'])) : $this->maxFileSize;
        $this->thumbTempExt = !empty($params['temp_ext']) ? strval($params['temp_ext']) : $this->thumbTempExt;
        $this->tempDir = !empty($params['temp_dir']) ? strval($params['temp_dir']) : $this->tempDir;
        return $this;
    }

    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    public function getMaxSize(): int
    {
        return $this->maxFileSize;
    }

    public function getTempExt(): string
    {
        return $this->thumbTempExt;
    }

    public function getTempDir(): string
    {
        return $this->tempDir;
    }
}
