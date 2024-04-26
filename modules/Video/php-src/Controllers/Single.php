<?php

namespace KWCMS\modules\Video\Controllers;


use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory as files_factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Node;
use kalanis\kw_images\Access\Factory as images_factory;
use kalanis\kw_images\Content\Images;
use kalanis\kw_images\ImagesException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mime\Check;
use kalanis\kw_mime\Interfaces\IMime;
use kalanis\kw_mime\MimeException;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\ArrayPath;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_routed_paths\StoreRouted;
use kalanis\kw_templates\TemplateException;
use kalanis\kw_tree\DataSources\Files;
use kalanis\kw_tree\Essentials\FileNode;
use kalanis\kw_user_paths\InnerLinks;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Core\Libs\ExternalLink;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Core\Libs\ImagesTranslations;
use KWCMS\modules\Video\Templates;


/**
 * Class Single
 * @package KWCMS\modules\Video\Controllers
 * Video gallery - single video
 */
class Single extends AModule
{
    protected string $module = 'Video';
    protected ExternalLink $linkExternal;
    protected Images $libImages;
    protected ArrayPath $arrPath;
    protected CompositeAdapter $files;
    protected InnerLinks $innerLink;
    protected Files $treeList;
    /** @var string[] */
    protected array $path = [];
    protected Templates\Player $templatePlayer;
    protected IMime $fileMime;
    protected string $fileToPlay = '';

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws FilesException
     * @throws ImagesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        Lang::load($this->module);
        Config::load($this->module);
        $this->linkExternal = new ExternalLink(StoreRouted::getPath());
        $this->files = (new files_factory(new FilesTranslations()))->getClass($constructParams);
        $this->fileMime = (new Check\Factory())->getLibrary(null);
        $this->libImages = (new images_factory(
            $this->files,
            $this->fileMime,
            new ImagesTranslations()
        ))->getImages($constructParams);
        $this->arrPath = new ArrayPath();
        $this->innerLink = new InnerLinks(
            StoreRouted::getPath(),
            boolval(Config::get('Core', 'site.more_users', false)),
            false,
            [],
            boolval(Config::get('Core', 'page.system_prefix', false)),
            boolval(Config::get('Core', 'page.data_separator', false))
        );
        $this->treeList = new Files($this->files);
        $this->templatePlayer = new Templates\Player();
    }

    /**
     * @throws PathsException
     * @throws FilesException
     */
    public function process(): void
    {
        $this->path = $this->pathLookup();
        $dir = $this->innerLink->toFullPath($this->arrPath->setArray($this->path)->getArrayDirectory());

        if ($this->files->isDir($dir)) {
            $this->treeList
                ->wantDeep(false)
                ->setStartPath($dir)
                ->setFilterCallback([$this, 'isUsable'])
                ->process();
        }
        if ($this->treeList->getRoot() && $this->treeList->getRoot()->getSubNodes()) {
            // video to load ...
            $video = $this->arrPath->setArray($this->path)->getFileName();
            if (in_array($video, $this->availableFiles())) {
                $this->fileToPlay = $video;
            }
        }
    }

    /**
     * @throws PathsException
     * @return string[]
     */
    protected function pathLookup(): array
    {
        if (empty($this->params['target'])) {
            return [];
        }
        $preset = strval($this->params['target']);
        return (IPaths::SPLITTER_SLASH != $preset[0])
            ? array_merge(StoreRouted::getPath()->getPath(), Stuff::linkToArray($preset)) // add current path to wanted content
            : Stuff::linkToArray(mb_substr($preset, 1)); // just remove that slash and return path
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

        return true;
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
        if ($this->fileToPlay) {
            $this->templatePlayer->setTemplateName('player');
            $thumb = $this->getThumb($this->path, Stuff::fileExt($this->fileToPlay));
            $link = $this->linkExternal->linkVariant($this->arrPath->setArray($this->path)->getString(), 'file', true);
            $mime = $this->fileMime->getMime($this->path);
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
        return $out->setContent($this->templatePlayer->render());
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
     * @param string $ext
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    protected function getIcon(string $ext): string
    {
        return $this->files->isFile($this->innerLink->toModulePath('Sysimage', ['images', 'files', $ext.'.png']))
            ? $this->linkExternal->linkVariant('files/'.$ext.'.png', 'sysimage', true)
            : $this->linkExternal->linkVariant('files/dummy.png', 'sysimage', true);
    }
}
