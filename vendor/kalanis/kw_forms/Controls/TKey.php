<?php

namespace kalanis\kw_forms\Controls;


trait TKey
{
    protected string $key = '';

    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
