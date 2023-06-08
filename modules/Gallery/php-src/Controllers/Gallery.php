<?php

namespace KWCMS\modules\Gallery\Controllers;


use kalanis\kw_confs\Config;
use kalanis\kw_files\Node;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Dirlist\Controllers\Dirlist;


/**
 * Class Gallery
 * @package KWCMS\modules\Gallery\Controllers
 * Gallery
 */
class Gallery extends Dirlist
{
    protected function defineModule(): void
    {
        $this->module = static::getClassName(static::class);
    }

    public function isUsable(Node $file): bool
    {
        if (empty(array_diff($file->getPath(), $this->dir))) {
            // root node must stay!
            return true;
        }

        $this->arrPath->setArray($file->getPath());
        if ('.' == $this->arrPath->getFileName()[0]) {
            return false;
        }

        if (!$file->isFile()) {
            return false;
        }

        // compare test only for lower suffixes
        $ext = strtolower(Stuff::fileExt($this->arrPath->getFileName()));
        $allowTypes = (array) Config::get($this->module, 'accept_types', ['jpg', 'jpeg', 'gif', 'png', 'bmp']);
        if (!in_array($ext, $allowTypes)) {
            return false;
        }

        if (!empty($this->preselectExt) && ($ext != $this->preselectExt)) {
            return false;
        }
        return true;
    }

    protected function getLink(array $path, string $ext): string
    {
        return
            $this->linkExternal->linkVariant(Stuff::arrayToLink($path), 'image', false)
            . '" target="_blank';
    }
}
