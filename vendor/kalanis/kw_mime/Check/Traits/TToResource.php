<?php

namespace kalanis\kw_mime\Check\Traits;


use kalanis\kw_mime\MimeException;


/**
 * Trait TToResource
 * @package kalanis\kw_mime\Check\Traits
 */
trait TToResource
{
    use TCheckCalls;

    /**
     * @param string $sourcePath
     * @param string|resource|bool|null $sourceData
     * @throws MimeException
     * @return resource
     */
    protected function readSourceToResource(string $sourcePath, $sourceData)
    {
        if ((false === $sourceData) || (null === $sourceData)) {
            throw new MimeException($this->getMiLang()->miCannotLoadFile($sourcePath));

        } elseif (!is_resource($sourceData)) {
            $stream = fopen('php://temp', 'rb+');

            if (false === $stream) {
                // @codeCoverageIgnoreStart
                throw new MimeException($this->getMiLang()->miCannotLoadFile($sourcePath));
            }
            // @codeCoverageIgnoreEnd

            rewind($stream);
            fwrite($stream, strval($sourceData));
            rewind($stream);

            return $stream;
        } else {
            return $sourceData;
        }
    }
}
