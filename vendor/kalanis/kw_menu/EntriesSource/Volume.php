<?php

namespace kalanis\kw_menu\EntriesSource;


use kalanis\kw_menu\Interfaces\IEntriesSource;
use kalanis\kw_paths\Stuff;
use Traversable;


/**
 * Class Volume
 * @package kalanis\kw_menu\EntriesSource
 * Entries source is processed directly over volume
 */
class Volume implements IEntriesSource
{
    use TFilterHtml;

    /** @var string path to menu dir */
    protected $rootPath = '';

    public function __construct(string $rootPath)
    {
        $this->rootPath = Stuff::removeEndingSlash($rootPath) . DIRECTORY_SEPARATOR;
    }

    public function getFiles(string $dir): Traversable
    {
        yield from array_filter(array_filter(scandir($this->rootPath . $dir), ['\kalanis\kw_paths\Stuff', 'notDots']), [$this, 'filterHtml']);
    }

    public function filterHtml(string $fileName): bool
    {
        return $this->filterExt(Stuff::fileExt($fileName));
    }
}
