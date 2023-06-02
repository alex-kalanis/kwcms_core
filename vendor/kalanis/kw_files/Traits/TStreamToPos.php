<?php

namespace kalanis\kw_files\Traits;


use kalanis\kw_files\FilesException;


/**
 * Trait TStreamToPos
 * @package kalanis\kw_menu\Traits
 * Write stream at expected position
 */
trait TStreamToPos
{
    use TLang;

    /**
     * @param resource $original stream with original content, usually file handler
     * @param resource $added stream with what will be added, also file handler
     * @param int|null $offset where it will be added
     * @throws FilesException
     * @return resource
     */
    public function addStreamToPosition($original, $added, ?int $offset = null)
    {
        if (!is_null($offset)) {
            // put it somewhere, left the rest intact
            $originalStat = fstat($original);
            if (false === $originalStat) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getLang()->flCannotOpenFile('temp'));
            }
            // @codeCoverageIgnoreEnd
            $originalLength = $originalStat['size'];

            $addedStat = fstat($added);
            if (false === $addedStat) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getLang()->flCannotOpenFile('temp'));
            }
            // @codeCoverageIgnoreEnd
            $addedLength = $addedStat['size'];

            // original data in the beginning
            $destination = fopen('php://temp', 'rb+'); // the target storage
            if (false === $destination) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getLang()->flCannotOpenFile('temp'));
            }
            // @codeCoverageIgnoreEnd
            if ((0 !== $offset) && (false === stream_copy_to_stream($original, $destination, $offset, 0))) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getLang()->flCannotWriteFile('temp'));
            }
            // @codeCoverageIgnoreEnd

            // filler - when the size is too much
            if ($originalLength < $offset) {
                $begin = fopen('php://temp', 'rb+');
                if (false === $begin) {
                    // @codeCoverageIgnoreStart
                    throw new FilesException($this->getLang()->flCannotOpenFile('temp'));
                }
                // @codeCoverageIgnoreEnd
                /** @scrutinizer ignore-unhandled */@fwrite($begin, str_repeat("\0", intval(abs($offset - $originalLength))));
                /** @scrutinizer ignore-unhandled */@rewind($begin);
                if (false === stream_copy_to_stream($begin, $destination, -1, 0)) {
                    // @codeCoverageIgnoreStart
                    throw new FilesException($this->getLang()->flCannotWriteFile('temp'));
                }
                // @codeCoverageIgnoreEnd

                $beginStat = fstat($begin);
                if (false === $beginStat) {
                    // @codeCoverageIgnoreStart
                    throw new FilesException($this->getLang()->flCannotOpenFile('temp'));
                }
                // @codeCoverageIgnoreEnd
                $originalLength += $beginStat['size'];
            }

            // amended data itself
            if (false === stream_copy_to_stream($added, $destination, -1, 0)) {
                // @codeCoverageIgnoreStart
                throw new FilesException($this->getLang()->flCannotWriteFile('temp'));
            }
            // @codeCoverageIgnoreEnd

            // the rest from the original
            if ($originalLength > $offset + $addedLength) {
                $rest = fopen('php://temp', 'rb+'); // the temporary storage for the end
                if (false === $rest) {
                    // @codeCoverageIgnoreStart
                    throw new FilesException($this->getLang()->flCannotOpenFile('temp'));
                }
                // @codeCoverageIgnoreEnd
                if (false === stream_copy_to_stream($original, $rest, -1, $offset + $addedLength)) {
                    // @codeCoverageIgnoreStart
                    throw new FilesException($this->getLang()->flCannotWriteFile('temp'));
                }
                /** @scrutinizer ignore-unhandled */@rewind($rest);
                // @codeCoverageIgnoreEnd
                if (false === stream_copy_to_stream($rest, $destination, -1, 0)) {
                    // @codeCoverageIgnoreStart
                    throw new FilesException($this->getLang()->flCannotWriteFile('temp'));
                }
                // @codeCoverageIgnoreEnd
            }
            return $destination;
        } else {
            return $added;
        }
    }
}
