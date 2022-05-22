<?php

namespace KWCMS\modules\Gallery;


use kalanis\kw_confs\Config;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Dirlist\Dirlist;


/**
 * Class Gallery
 * @package KWCMS\modules\Contact
 * Gallery
 */
class Gallery extends Dirlist
{
    protected function defineModule(): void
    {
        $this->module = static::getClassName(static::class);
    }

    protected function pathLookup(): string
    {
        return !empty($this->params['path'])
            ? Stuff::fileBase(Stuff::arrayToPath(Stuff::linkToArray($this->params['path'])))
            : Stuff::fileBase(Config::getPath()->getPath()) ; # use dir path
    }

    public function isUsable(string $file): bool
    {
        if ('.' == $file[0]) {
            return false;
        }

        if (!is_file($this->dir . DIRECTORY_SEPARATOR . $file)) {
            return false;
        }

        $ext = strtolower(Stuff::fileExt($file)); # compare test only for lower suffixes
        $allowTypes = (array)Config::get($this->module, 'accept_types', ['jpg', 'jpeg', 'gif', 'png', 'bmp']);
        if (!in_array($ext, $allowTypes)) {
            return false;
        }

        if (!empty($this->preselectExt) && ($ext != $this->preselectExt)) {
            return false;
        }
        return true;
    }

    protected function getLink(string $file): string
    {
        return
            $this->linkExternal->linkVariant($this->path . IPaths::SPLITTER_SLASH . $file, 'image', false)
            . '" target="_blank';
    }

    protected function getThumb(string $file): string
    {
        $want = $this->libFiles->getLibThumb()->getPath($this->path . DIRECTORY_SEPARATOR . $file);
        return $this->linkInternal->userContent($want)
            ? $this->linkExternal->linkVariant($want, 'Image', true)
            : $this->getIcon($file) ;
    }
}
