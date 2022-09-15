<?php

namespace kalanis\kw_files\Processing;


use kalanis\kw_files\FilesException;

/**
 * trait TPathTransform
 * @package kalanis\kw_files\Processing
 * Transform path from string to array and back
 */
trait TPathTransform
{
    /**
     * @param array<string> $path
     * @param string $pathDelimiter
     * @throws FilesException
     * @return string
     */
    public function compactName(array $path, string $pathDelimiter = DIRECTORY_SEPARATOR): string
    {
        if (empty($pathDelimiter)) {
            throw new FilesException('You set the empty directory delimiter!');
        }
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

    /**
     * @param string $path
     * @param string $pathDelimiter
     * @throws FilesException
     * @return array<string>
     */
    public function expandName(string $path, string $pathDelimiter = DIRECTORY_SEPARATOR): array
    {
        $extraDelimiter = "--\e--";
        if (empty($pathDelimiter)) {
            throw new FilesException('You set the empty directory delimiter!');
        }
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
