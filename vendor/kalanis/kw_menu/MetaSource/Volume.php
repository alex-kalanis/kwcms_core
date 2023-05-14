<?php

namespace kalanis\kw_menu\MetaSource;


use kalanis\kw_menu\Interfaces;
use kalanis\kw_menu\Menu\Menu;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\Translations;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;


/**
 * Class Volume
 * @package kalanis\kw_menu\MetaSource
 * Data source is processed directly over volume
 */
class Volume implements Interfaces\IMetaSource
{
    /** @var string path to menu dir */
    protected $metaDir = '';
    /** @var string[] name of the file */
    protected $metaFile = [];
    /** @var Interfaces\IMNTranslations */
    protected $lang = null;
    /** @var Interfaces\IMetaFileParser */
    protected $parser = null;

    public function __construct(string $metaPath, Interfaces\IMetaFileParser $parser, ?Interfaces\IMNTranslations $lang = null)
    {
        $this->metaDir = $metaPath;
        $this->parser = $parser;
        $this->lang = $lang ?: new Translations();
    }

    public function setSource(array $metaSource): void
    {
        $this->metaFile = $metaSource;
    }

    public function exists(): bool
    {
        try {
            $path = $this->metaDir . Stuff::arrayToPath($this->metaFile, $this->systemDelimiter());
            return is_file($path);
        } catch (PathsException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function load(): Menu
    {
        try {
            $path = $this->metaDir . Stuff::arrayToPath($this->metaFile, $this->systemDelimiter());
            $content = @file_get_contents($path);
            if (false === $content) {
                throw new MenuException($this->lang->mnCannotOpen());
            }
            return $this->parser->unpack($content);
        } catch (PathsException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function save(Menu $content): bool
    {
        try {
            $path = $this->metaDir . Stuff::arrayToPath($this->metaFile, $this->systemDelimiter());
            if (false === @file_put_contents($path, $this->parser->pack($content))) {
                throw new MenuException($this->lang->mnCannotSave());
            }
            return true;
        } catch (PathsException $ex) {
            throw new MenuException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * Because tests - fail Stuff class
     * @return string
     */
    protected function systemDelimiter(): string
    {
        return DIRECTORY_SEPARATOR;
    }
}
