<?php

namespace KWCMS\modules\Video;


use kalanis\kw_address_handler\Handler;
use kalanis\kw_address_handler\Sources;
use kalanis\kw_confs\Config;
use kalanis\kw_mime\MimeType;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\Stuff;
use KWCMS\modules\Dirlist\Dirlist;


/**
 * Class Video
 * @package KWCMS\modules\Contact
 * Video gallery
 */
class Video extends Dirlist
{
    /** @var Handler|null */
    protected $currentPageHandler = null;
    /** @var Templates\Player */
    protected $templatePlayer = null;
    /** @var MimeType */
    protected $fileMime = null;
    protected $fileToPlay = '';

    public function __construct()
    {
        parent::__construct();
        $this->templateDisplay = new Templates\Display();
        $this->templatePlayer = new Templates\Player();
        $this->fileMime = new MimeType(true);
    }

    protected function defineModule(): void
    {
        $this->module = static::getClassName(static::class);
    }

    public function process(): void
    {
        parent::process();
        if ($this->dirList) {
            # video to load ...
            $videos = $this->inputs->getInArray('v');
            if (!empty($videos)) {
                $video = strval(reset($videos));
                if (!empty($video)) {
                    $filesAvailable = $this->dirList->getFiles($this->dirList->getPaging()->getPager()->getMaxResults());
                    if (in_array($video, $filesAvailable)) {
                        $this->fileToPlay = $video;
                    }
                }
            }
        }
        $this->currentPageHandler = new Handler(new Sources\Inputs($this->inputs));
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
        $allowTypes = (array)Config::get($this->module, 'accept_types', ['avi', 'mpeg', 'mpg', 'wmv', 'mp4', 'webm']);
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
        $params = $this->currentPageHandler->getParams();
        $params->offsetSet('v', $file);
        return $this->currentPageHandler->getAddress();
    }

    protected function getThumb(string $file): string
    {
        $fileName = Stuff::fileBase($file);
        $want = $this->libFiles->getLibThumb()->getPath($this->path . DIRECTORY_SEPARATOR . $fileName);
        if ($this->linkInternal->userContent($want . '.jpg')) {
            return $this->linkExternal->linkVariant($want . '.jpg', 'Image', true);
        }
        if ($this->linkInternal->userContent($want . '.jpeg')) {
            return $this->linkExternal->linkVariant($want . '.jpeg', 'Image', true);
        }
        if ($this->linkInternal->userContent($want . '.png')) {
            return $this->linkExternal->linkVariant($want . '.png', 'Image', true);
        }
        return $this->getIcon($file);
    }

    public function output(): Output\AOutput
    {
        $out = new Output\Html();
        if ($this->fileToPlay) {
            $this->templatePlayer->setTemplateName('player');
            $thumb = $this->getThumb($this->fileToPlay);
            $link = $this->linkExternal->linkVariant($this->path . '/' . $this->fileToPlay, 'file', true);
            $mime = $this->fileMime->mimeByExt(Stuff::fileExt($this->fileToPlay));
        } else {
            $this->templatePlayer->setTemplateName('nothing');
            $thumb = $this->linkExternal->linkVariant('video/free.png', 'sysimage', true);
            $link = '';
            $mime = '';
        }
        $this->templatePlayer->setData(
            $thumb,
            intval(Config::get($this->module, 'width', 100)),
            intval(Config::get($this->module, 'height', 100)),
            $link,
            $mime
        );
        return $out->setContent($this->templatePlayer->render() . parent::output()->output());
    }
}
