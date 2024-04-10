<?php

namespace kalanis\kw_files\Traits;


use kalanis\kw_files\FilesException;


/**
 * Trait TToString
 * @package kalanis\kw_menu\Traits
 * Transform resource to string
 */
trait TToString
{
    use TLang;

    /**
     * @param string $target
     * @param mixed $content
     * @throws FilesException
     * @return string
     */
    protected function toString(string $target, $content): string
    {
        if (is_null($content)) {
            throw new FilesException($this->getFlLang()->flCannotLoadFile($target));
        } elseif (is_bool($content)) {
            throw new FilesException($this->getFlLang()->flCannotLoadFile($target));
        } elseif (is_resource($content)) {
            rewind($content);
            $data = stream_get_contents($content, -1, 0);
            if (false === $data) {
                // @codeCoverageIgnoreStart
                // must die something with stream reading
                throw new FilesException($this->getFlLang()->flCannotLoadFile($target));
            }
            // @codeCoverageIgnoreEnd
            return strval($data);
        } else {
            try {
                return strval($content);
            } catch (\Error $ex) {
                throw new FilesException($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
    }
}
