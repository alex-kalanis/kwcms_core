<?php

namespace kalanis\kw_files\Traits;


use kalanis\kw_files\FilesException;


/**
 * Trait TToStream
 * @package kalanis\kw_menu\Traits
 * Transform resource to stream with handler
 */
trait TToStream
{
    use TLang;

    /**
     * @param string $target
     * @param mixed $content
     * @throws FilesException
     * @return resource
     */
    protected function toStream(string $target, $content)
    {
        if (is_null($content)) {
            throw new FilesException($this->getLang()->flCannotLoadFile($target));
        } elseif (is_bool($content)) {
            throw new FilesException($this->getLang()->flCannotLoadFile($target));
        } elseif (is_object($content)) {
            throw new FilesException($this->getLang()->flCannotLoadFile($target));
        } elseif (is_resource($content)) {
            rewind($content);
            return $content;
        } else {
            $handle = fopen('php://temp', 'rb+');
            if (false === $handle) {
                // @codeCoverageIgnoreStart
                // must die something with stream reading
                throw new FilesException($this->getLang()->flCannotLoadFile($target));
            }
            // @codeCoverageIgnoreEnd
            if (false === fwrite($handle, strval($content))) {
                // @codeCoverageIgnoreStart
                // must die something with stream reading
                throw new FilesException($this->getLang()->flCannotLoadFile($target));
            }
            // @codeCoverageIgnoreEnd
            return $handle;
        }
    }
}
