<?php

namespace kalanis\kw_menu\MetaSource;


use kalanis\kw_menu\Interfaces;
use kalanis\kw_menu\Menu\Menu;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\Translations;


/**
 * Class Volume
 * @package kalanis\kw_menu\MetaSource
 * Data source is processed directly over volume
 */
class Volume implements Interfaces\IMetaSource
{
    /** @var string path to menu dir */
    protected $metaDir = '';
    /** @var string name of the file */
    protected $metaFile = '';
    /** @var Interfaces\IMNTranslations */
    protected $lang = null;
    /** @var Interfaces\IMetaFileParser */
    protected $parser = null;

    public function __construct(string $metaPath, Interfaces\IMetaFileParser $parser, ?Interfaces\IMNTranslations $lang = null)
    {
        $this->metaDir = $metaPath;
        $this->metaFile = '';
        $this->parser = $parser;
        $this->lang = $lang ?: new Translations();
    }

    public function setSource(string $metaSource): void
    {
        $this->metaFile = $metaSource;
    }

    public function exists(): bool
    {
        return is_file($this->metaDir . $this->metaFile);
    }

    public function load(): Menu
    {
        $content = @file_get_contents($this->metaDir . $this->metaFile);
        if (false === $content) {
            throw new MenuException($this->lang->mnCannotOpen());
        }
        return $this->parser->unpack($content);
    }

    public function save(Menu $content): bool
    {
        if (false === @file_put_contents($this->metaDir . $this->metaFile, $this->parser->pack($content))) {
            throw new MenuException($this->lang->mnCannotSave());
        }
        return true;
    }
}
