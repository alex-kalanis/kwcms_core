<?php

namespace kalanis\kw_images\Configs;


/**
 * Class ConfigFactory
 * Configuration for the whole processor
 * @package kalanis\kw_images\Graphics
 */
class ConfigFactory
{
    protected ProcessorConfig $processorConfig;
    protected ImageConfig $imageConfig;
    protected ThumbConfig $thumbConfig;

    public function __construct(
        ?ProcessorConfig $processorConfig = null,
        ?ImageConfig $imageConfig = null,
        ?ThumbConfig $thumbConfig = null
    )
    {
        $this->processorConfig = $processorConfig ?: new ProcessorConfig();
        $this->imageConfig = $imageConfig ?: new ImageConfig();
        $this->thumbConfig = $thumbConfig ?: new ThumbConfig();
    }

    /**
     * @param mixed $params
     * @return $this
     */
    public function setData($params): self
    {
        if (is_array($params)) {
            if (isset($params['images'])) {
                $this->setData($params['images']);
            }
            $this->processorConfig->setData($params);
            $this->imageConfig->setData($params);
            $this->thumbConfig->setData($params);
        }
        return $this;
    }

    public function getProcessor(): ProcessorConfig
    {
        return $this->processorConfig;
    }

    public function getImage(): ImageConfig
    {
        return $this->imageConfig;
    }

    public function getThumb(): ThumbConfig
    {
        return $this->thumbConfig;
    }
}
