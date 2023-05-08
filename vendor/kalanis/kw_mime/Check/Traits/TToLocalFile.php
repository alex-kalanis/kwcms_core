<?php

namespace kalanis\kw_mime\Check\Traits;


use kalanis\kw_mime\MimeException;


/**
 * Trait TToLocalFile
 * @package kalanis\kw_mime\Check\Traits
 */
trait TToLocalFile
{
    use TCheckCalls;

    /**
     * @param string $sourcePath
     * @param string|resource|bool|null $sourceData
     * @param string $targetLocalFile
     * @throws MimeException
     */
    protected function readSourceToLocalFile(string $sourcePath, $sourceData, string $targetLocalFile): void
    {
        if ((false === $sourceData) || (null === $sourceData)) {
            throw new MimeException($this->getMiLang()->miCannotLoadFile($sourcePath));
        }

        $stream = fopen($targetLocalFile, 'rb+');
        if (false === $stream) {
            // @codeCoverageIgnoreStart
            throw new MimeException($this->getMiLang()->miCannotLoadTempFile());
        }
        // @codeCoverageIgnoreEnd

        if (is_resource($sourceData)) {
            rewind($sourceData);
            if (false === stream_copy_to_stream($sourceData, $stream, -1, 0)) {
                // @codeCoverageIgnoreStart
                throw new MimeException($this->getMiLang()->miCannotGetFilePart($sourcePath));
            }
            // @codeCoverageIgnoreEnd

            rewind($stream);
            fclose($stream);

        } else {
            fwrite($stream, strval($sourceData));
            fclose($stream);
        }
    }
}
