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
    const FILE_TEMP = 'thumb_tmp';

    protected int $maxWidth = 180;
    protected int $maxHeight = 180;
    protected int $maxFileSize = 10485760;
    protected string $tempPrefix = self::FILE_TEMP;

    /**
     * @param array<string, string|int> $params
     * @return $this
     */
    public function setData(array $params = []): self
    {
        $this->maxWidth = !empty($params['tmb_width']) ? intval(strval($params['tmb_width'])) : $this->maxWidth;
        $this->maxHeight = !empty($params['tmb_height']) ? intval(strval($params['tmb_height'])) : $this->maxHeight;
        $this->maxFileSize = !empty($params['tmb_size']) ? intval(strval($params['tmb_size'])) : $this->maxFileSize;
        $this->tempPrefix = !empty($params['temp_pref']) ? strval($params['temp_pref']) : $this->tempPrefix;
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

    public function getTempPrefix(): string
    {
        return $this->tempPrefix;
    }
}
