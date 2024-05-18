<?php

namespace KWCMS\modules\Video\Controllers;


use kalanis\kw_address_handler\Handler;
use kalanis\kw_address_handler\Sources;
use kalanis\kw_confs\Config;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Node;
use kalanis\kw_mime\Check;
use kalanis\kw_mime\Interfaces\IMime;
use kalanis\kw_mime\MimeException;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_templates\TemplateException;
use kalanis\kw_tree\Essentials\FileNode;
use KWCMS\modules\Dirlist\Controllers\Dirlist;
use KWCMS\modules\Video\Templates;


/**
 * Class Video
 * @package KWCMS\modules\Video\Controllers
 * Video gallery
 */
class Video extends Dirlist
{
    protected ?Handler $currentPageHandler;
    protected Templates\Player $templatePlayer;
    protected IMime $fileMime;
    protected string $fileToPlay = '';

    public function __construct(...$constructParams)
    {
        parent::__construct(...$constructParams);
        $this->templateDisplay = new Templates\Display();
        $this->templatePlayer = new Templates\Player();
        $this->fileMime = (new Check\Factory())->getLibrary(null);
    }

    protected function defineModule(): void
    {
        $this->module = static::getClassName(static::class);
    }

    public function process(): void
    {
        parent::process();
        if ($this->treeList->getRoot() && $this->treeList->getRoot()->getSubNodes()) {
            // video to load ...
            $videos = $this->inputs->getInArray('v');
            if (!empty($videos)) {
                $video = strval(reset($videos));
                if (!empty($video)) {
                    if (in_array($video, $this->availableFiles())) {
                        $this->fileToPlay = $video;
                    }
                }
            }
        }
        $this->currentPageHandler = new Handler(new Sources\Inputs($this->inputs));
    }

    protected function availableFiles(): array
    {
        return array_map([$this, 'fileName'], $this->treeList->getRoot()->getSubNodes());
    }

    public function fileName(FileNode $node): string
    {
        return $this->arrPath->setArray($node->getPath())->getFileName();
    }

    public function isUsable(Node $file): bool
    {
        if (!$file->isFile()) {
            return false;
        }

        $this->arrPath->setArray($file->getPath());
        if ('.' == $this->arrPath->getFileName()[0]) {
            return false;
        }

        // compare test only for lower suffixes
        $ext = strtolower(Stuff::fileExt($this->arrPath->getFileName()));
        $allowTypes = (array) Config::get($this->module, 'accept_types', ['avi', 'mpeg', 'mpg', 'wmv', 'mp4', 'webm']);
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
        $params = $this->currentPageHandler->getParams();
        $params->offsetSet('v', $this->arrPath->setArray($path)->getFileName());
        return $this->currentPageHandler->getAddress();
    }

    /**
     * @param string[] $path
     * @param string $ext
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    protected function getThumb(array $path, string $ext): string
    {
        $tmbPath = $this->libImages->reverseThumbPath($path);
        $extPath = $this->arrPath->setArray($tmbPath)->getString();
        $tmbDir = $this->arrPath->getArrayDirectory();
        $tmbFile = Stuff::fileBase($this->arrPath->getFileName());

        if ($this->files->isFile($this->innerLink->toFullPath(array_merge($tmbDir, [$tmbFile . '.jpg'])))) {
            return $this->linkExternal->linkVariant(Stuff::fileBase($extPath) . '.jpg', 'Image', true);
        }
        if ($this->files->isFile($this->innerLink->toFullPath(array_merge($tmbDir, [$tmbFile . '.jpeg'])))) {
            return $this->linkExternal->linkVariant(Stuff::fileBase($extPath) . '.jpeg', 'Image', true);
        }
        if ($this->files->isFile($this->innerLink->toFullPath(array_merge($tmbDir, [$tmbFile . '.png'])))) {
            return $this->linkExternal->linkVariant(Stuff::fileBase($extPath) . '.png', 'Image', true);
        }
        return $this->getIcon($ext);
    }

    /**
     * @throws FilesException
     * @throws MimeException
     * @throws PathsException
     * @throws TemplateException
     * @return Output\AOutput
     */
    public function output(): Output\AOutput
    {
        $out = new Output\Html();
        if (!empty($this->fileToPlay)) {
            $this->templatePlayer->setTemplateName('player');
            $path = array_merge($this->path, [$this->fileToPlay]);
            $thumb = $this->getThumb($path, Stuff::fileExt($this->fileToPlay));
            $link = $this->linkExternal->linkVariant(Stuff::arrayToLink($path), 'file', true);
            $mime = $this->fileMime->getMime($path);
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
