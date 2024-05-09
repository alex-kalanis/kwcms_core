<?php

namespace kalanis\kw_forms\Adapters;


use kalanis\kw_input\Interfaces\IFileEntry;


class FileEntry implements IFileEntry
{
    protected string $key = '';
    protected string $value = '';
    protected string $temp = '';
    protected string $mime = '';
    protected int $error = 0;
    protected int $size = 0;

    public function setData(string $key, string $value, string $temp, string $mime, int $error, int $size): self
    {
        $this->key = $key;
        $this->value = $value;
        $this->temp = $temp;
        $this->mime = $mime;
        $this->error = $error;
        $this->size = $size;
        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getMimeType(): string
    {
        return $this->mime;
    }

    public function getTempName(): string
    {
        return $this->temp;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getSource(): string
    {
        return static::SOURCE_FILES;
    }
}
