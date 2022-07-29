<?php

namespace kalanis\kw_images\Graphics;


/**
 * Class DirConfig
 * File thumbnail configuration in dirs
 * @package kalanis\kw_images\Graphics
 */
class DirConfig
{
    const FILE_TEMP = '.png';

    /** @var string */
    protected $thumbExt = self::FILE_TEMP;

    /**
     * @param array<string, string|int> $params
     * @return $this
     */
    public function setData(array $params = []): self
    {
        $this->thumbExt = !empty($params['tmb_ext']) ? strval($params['tmb_ext']) : $this->thumbExt;
        return $this;
    }

    public function getThumbExt(): string
    {
        return $this->thumbExt;
    }
}
