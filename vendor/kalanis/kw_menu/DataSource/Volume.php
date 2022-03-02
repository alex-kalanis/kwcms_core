<?php

namespace kalanis\kw_menu\DataSource;


use kalanis\kw_menu\Interfaces\IDataSource;
use kalanis\kw_menu\Interfaces\IMNTranslations;
use kalanis\kw_menu\MenuException;
use kalanis\kw_menu\Translations;
use kalanis\kw_paths\Stuff;
use Traversable;


/**
 * Class Volume
 * @package kalanis\kw_menu\DataSource
 * Data source is processed directly over volume
 */
class Volume implements IDataSource
{
    /** @var string path to menu dir */
    protected $rootPath = '';
    /** @var IMNTranslations */
    protected $lang = null;

    public function __construct(string $rootPath, ?IMNTranslations $lang = null)
    {
        $this->rootPath = Stuff::removeEndingSlash($rootPath) . DIRECTORY_SEPARATOR;
        $this->lang = $lang ?: new Translations();
    }

    public function exists(string $metaFile): bool
    {
        return is_file($this->rootPath . $metaFile);
    }

    public function load(string $metaFile): string
    {
        $content = @file_get_contents($this->rootPath . $metaFile);
        if (false === $content) {
            throw new MenuException($this->lang->mnCannotOpen());
        }
        return $content;
    }

    public function save(string $metaFile, string $content): bool
    {
        if (false === @file_put_contents($this->rootPath . $metaFile, $content)) {
            throw new MenuException($this->lang->mnCannotSave());
        }
        return true;
    }

    public function getFiles(string $dir): Traversable
    {
        yield from array_filter(array_filter(scandir($this->rootPath . $dir), ['\kalanis\kw_paths\Stuff', 'notDots']), [$this, 'filterHtml']);
    }

    public function filterHtml(string $file): bool
    {
        return in_array(Stuff::fileExt($file), ['htm', 'html']);
    }
}
