<?php

namespace kalanis\kw_images\Graphics;


use kalanis\kw_images\Interfaces\ISizes;


/**
 * Class ImageConfig
 * Configuration for main image itself
 * @package kalanis\kw_images\Graphics
 */
class ImageConfig implements ISizes
{
    /** @var int */
    protected $maxWidth = 1024;
    /** @var int */
    protected $maxHeight = 1024;
    /** @var int */
    protected $maxFileSize = 10485760;
    /** @var string */
    protected $tempPrefix = '';

    /**
     * @param array<string, string|int> $params
     * @return $this
     */
    public function setData(array $params = []): self
    {
        $this->maxWidth = !empty($params['max_width']) ? intval(strval($params['max_width'])) : $this->maxWidth;
        $this->maxHeight = !empty($params['max_height']) ? intval(strval($params['max_height'])) : $this->maxHeight;
        $this->maxFileSize = !empty($params['max_size']) ? intval(strval($params['max_size'])) : $this->maxFileSize;
        $this->tempPrefix = !empty($params['tmp_pref']) ? strval($params['tmp_pref']) : $this->tempPrefix;
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
