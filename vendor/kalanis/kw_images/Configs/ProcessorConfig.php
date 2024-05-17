<?php

namespace kalanis\kw_images\Configs;


/**
 * Class ProcessorConfig
 * Configuration for the whole processor
 * @package kalanis\kw_images\Graphics
 */
class ProcessorConfig
{
    protected bool $createThumb = true;
    protected bool $wantLimitSize = false;
    protected bool $wantLimitExt = false;
    protected string $defaultExt = 'jpg';

    /**
     * @param array<string, string|int> $params
     * @return $this
     */
    public function setData(array $params = []): self
    {
        $this->createThumb = isset($params['create_thumb']) ? boolval(intval(strval($params['create_thumb']))) : $this->createThumb;
        $this->wantLimitSize = isset($params['want_limit_size']) ? boolval(intval(strval($params['want_limit_size']))) : $this->wantLimitSize;
        $this->wantLimitExt = isset($params['want_limit_ext']) ? boolval(intval(strval($params['want_limit_ext']))) : $this->wantLimitExt;
        $this->defaultExt = !empty($params['default_ext']) ? strval($params['default_ext']) : $this->defaultExt;
        return $this;
    }

    public function getDefaultExt(): string
    {
        return $this->defaultExt;
    }

    public function canLimitExt(): bool
    {
        return $this->wantLimitExt;
    }

    public function canLimitSize(): bool
    {
        return $this->wantLimitSize;
    }

    public function hasThumb(): bool
    {
        return $this->createThumb;
    }
}
