<?php

namespace kalanis\kw_mime\Check\Traits;


use kalanis\kw_mime\MimeException;


/**
 * Trait TToString
 * @package kalanis\kw_mime\Check\Traits
 */
trait TToString
{
    use TCheckCalls;

    /**
     * @param string $sourcePath
     * @param string|resource|bool|null $sourceData
     * @throws MimeException
     * @return string
     */
    protected function readSourceToString(string $sourcePath, $sourceData): string
    {
        if ((false === $sourceData) || (null === $sourceData)) {
            throw new MimeException($this->getMiLang()->miCannotLoadFile($sourcePath));

        } elseif (is_resource($sourceData)) {
            rewind($sourceData);

            $data = stream_get_contents($sourceData, -1, 0);
            if (false === $data) {
                // @codeCoverageIgnoreStart
                throw new MimeException($this->getMiLang()->miCannotGetFilePart($sourcePath));
            }
            // @codeCoverageIgnoreEnd

            return $data;
        } else {
            return strval($sourceData);
        }
    }
}
