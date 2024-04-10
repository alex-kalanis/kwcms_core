<?php

namespace kalanis\kw_menu\EntriesSource;


use kalanis\kw_menu\Interfaces\IEntriesSource;
use kalanis\kw_menu\Traits\TFilterHtml;
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
    protected string $rootPath = '';

    public function __construct(string $rootPath)
    {
        $this->rootPath = Stuff::removeEndingSlash($rootPath) . DIRECTORY_SEPARATOR;
    }

    public function getFiles(array $path): Traversable
    {
        $dir = Stuff::arrayToPath($path);
        $list = scandir($this->rootPath . $dir);
        if (false !== $list) {
            yield from array_filter(array_filter($list, [Stuff::class, 'notDots']), [$this, 'filterHtml']);
        }
    }

    public function filterHtml(string $fileName): bool
    {
        return $this->filterExt(Stuff::fileExt($fileName));
    }
}
