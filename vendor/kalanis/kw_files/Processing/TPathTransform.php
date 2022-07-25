<?php

namespace kalanis\kw_files\Processing;


/**
 * trait TPathTransform
 * @package kalanis\kw_files\Processing
 * Transform path from string to array and back
 */
trait TPathTransform
{
    /** @var string */
    protected $pathDelimiter = DIRECTORY_SEPARATOR;

    public function compactName(array $path, string $pathDelimiter = DIRECTORY_SEPARATOR): string
    {
        return implode(
            $pathDelimiter,
            str_replace(
                $pathDelimiter,
                $this->getEscapeChar() . $pathDelimiter,
                str_replace(
                    $this->getEscapeChar(),
                    $this->getEscapeChar() . $this->getEscapeChar(),
                    $path
                )
            )
        );
    }

    public function expandName(string $path, string $pathDelimiter = DIRECTORY_SEPARATOR): array
    {
        $extraDelimiter = "--\e--";
        $path = str_replace($this->getEscapeChar() . $this->getEscapeChar(), $extraDelimiter . $extraDelimiter, $path);
        $path = str_replace($this->getEscapeChar() . $pathDelimiter, $this->getEscapeChar() . $extraDelimiter, $path);
        $arr = explode($pathDelimiter, $path);
        $arr = str_replace($this->getEscapeChar() . $extraDelimiter, $pathDelimiter, $arr);
        $arr = str_replace($extraDelimiter . $extraDelimiter, $this->getEscapeChar(), $arr);
        return $arr;
    }

    protected function getEscapeChar(): string
    {
        return '\\';
    }
}
