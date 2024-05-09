<?php

namespace kalanis\kw_mime;


use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\PathsException;


/**
 * Class MimeType
 * @package kalanis\kw_mime
 */
class MimeType
{
    protected bool $customAtFirst = false;

    public function __construct(bool $customAtFirst = false)
    {
        $this->customAtFirst = $customAtFirst;
    }

    /**
     * @param string $path
     * @throws MimeException
     * @throws PathsException
     * @return string
     */
    public function mimeByPath(string $path): string
    {
        $pt = new ArrayPath();
        $pt->setString($path);
        if ($this->customAtFirst) {
            $lib0 = new Check\CustomList();
            return $lib0->getMime($pt->getArray());
        }

        try {
            $lib1 = new Check\LocalVolume1();
            $lib1->canUse(null);
            return $lib1->getMime($pt->getArray());
            // @codeCoverageIgnoreStart
        } catch (MimeException $ex) {
            // pass
        }
        // @codeCoverageIgnoreEnd

        // @codeCoverageIgnoreStart
        try {
            $lib2 = new Check\LocalVolume2();
            $lib2->canUse(null);
            return $lib2->getMime($pt->getArray());
        } catch (MimeException $ex) {
            // pass
        }
        // @codeCoverageIgnoreEnd

        // @codeCoverageIgnoreStart
        // same as in localAtFirst
        $libC = new Check\CustomList();
        return $libC->getMime($pt->getArray());
    }
    // @codeCoverageIgnoreEnd

    public function mimeByExt(string $ext): string
    {
        $lib = new Check\CustomList();
        return $lib->mimeByExt($ext);
    }
}
