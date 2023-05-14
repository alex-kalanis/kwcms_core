<?php

namespace kalanis\kw_menu\Traits;


use kalanis\kw_menu\MenuException;


/**
 * Trait TToString
 * @package kalanis\kw_menu\Traits
 * Transform resource to string
 */
trait TToString
{
    use TLang;

    /**
     * @param mixed $content
     * @throws MenuException
     * @return string
     */
    protected function toString($content): string
    {
        if (is_null($content)) {
            throw new MenuException($this->getMnLang()->mnProblematicData());
        } elseif (is_bool($content)) {
            throw new MenuException($this->getMnLang()->mnProblematicData());
        } elseif (is_resource($content)) {
            rewind($content);
            $data = stream_get_contents($content, -1, 0);
            if (false === $data) {
                // @codeCoverageIgnoreStart
                // must die something with stream reading
                throw new MenuException($this->getMnLang()->mnCannotOpen());
            }
            // @codeCoverageIgnoreEnd
            return strval($data);
        } else {
            try {
                return strval($content);
            } catch (\Error $ex) {
                throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
    }
}
