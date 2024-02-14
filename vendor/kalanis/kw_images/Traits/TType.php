<?php

namespace kalanis\kw_images\Traits;


use kalanis\kw_images\ImagesException;
use kalanis\kw_images\Interfaces\IIMTranslations;
use kalanis\kw_mime\Interfaces\IMime;
use kalanis\kw_mime\MimeException;


/**
 * Trait TType
 * Work with different image types
 * @package kalanis\kw_images\Traits
 */
trait TType
{
    use TLang;

    /** @var IMime */
    protected $libMime = null;

    public function initType(IMime $libMime, ?IIMTranslations $lang = null): void
    {
        $this->setImLang($lang);
        $this->libMime = $libMime;
    }

    /**
     * @param string[] $path
     * @throws ImagesException
     * @throws MimeException
     * @return string
     */
    protected function getType(array $path): string
    {
        $mime = $this->libMime->getMime($path);
        list($type, $app) = explode('/', $mime);
        if ('image' !== $type) {
            throw new ImagesException($this->getImLang()->imWrongMime($mime));
        }
        return $app;
    }
}
