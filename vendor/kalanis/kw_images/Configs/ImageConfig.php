<?php

namespace kalanis\kw_images\Configs;


use kalanis\kw_images\Interfaces\ISizes;


/**
 * Class ImageConfig
 * Configuration for main image itself
 * @package kalanis\kw_images\Graphics
 */
class ImageConfig implements ISizes
{
    protected int $maxInWidth = 16384;
    protected int $maxInHeight = 16384;
    protected int $maxStoreWidth = 1024;
    protected int $maxStoreHeight = 1024;
    protected int $maxFileSize = 10485760;
    protected string $tempPrefix = '';

    /**
     * @param array<string, string|int> $params
     * @return $this
     */
    public function setData(array $params = []): self
    {
        $this->maxInWidth = !empty($params['max_upload_width']) ? intval(strval($params['max_upload_width'])) : $this->maxInWidth;
        $this->maxInHeight = !empty($params['max_upload_height']) ? intval(strval($params['max_upload_height'])) : $this->maxInHeight;
        $this->maxStoreWidth = !empty($params['max_width']) ? intval(strval($params['max_width'])) : $this->maxStoreWidth;
        $this->maxStoreHeight = !empty($params['max_height']) ? intval(strval($params['max_height'])) : $this->maxStoreHeight;
        $this->maxFileSize = !empty($params['max_size']) ? intval(strval($params['max_size'])) : $this->maxFileSize;
        $this->tempPrefix = !empty($params['tmp_pref']) ? strval($params['tmp_pref']) : $this->tempPrefix;
        return $this;
    }

    public function getMaxInWidth(): int
    {
        return $this->maxInWidth;
    }

    public function getMaxInHeight(): int
    {
        return $this->maxInHeight;
    }

    public function getMaxStoreWidth(): int
    {
        return $this->maxStoreWidth;
    }

    public function getMaxStoreHeight(): int
    {
        return $this->maxStoreHeight;
    }

    public function getMaxFileSize(): int
    {
        return $this->maxFileSize;
    }

    public function getTempPrefix(): string
    {
        return $this->tempPrefix;
    }
}
