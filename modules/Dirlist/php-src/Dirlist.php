<?php

namespace KWCMS\modules\Dirlist;


use kalanis\kw_confs\Config;
use kalanis\kw_images\Files;
use kalanis\kw_images\FilesHelper;
use kalanis\kw_langs\Lang;
use kalanis\kw_listing\DirectoryListing;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_modules\InternalLink;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_paths\Interfaces\IPaths;
use kalanis\kw_paths\Stuff;


/**
 * Class Dirlist
 * @package KWCMS\modules\Dirlist
 * Listing the directory
 */
class Dirlist extends AModule
{
    protected $module = '';
    /** @var Templates\Main */
    protected $templateMain = null;
    /** @var Templates\Row */
    protected $templateRow = null;
    /** @var Templates\Display */
    protected $templateDisplay = null;
    /** @var InternalLink */
    protected $linkInternal = null;
    /** @var ExternalLink */
    protected $linkExternal = null;
    /** @var DirectoryListing|null */
    protected $dirList = null;
    /** @var Files|null */
    protected $libFiles = null;

    protected $path = '';
    protected $dir = '';
    protected $preselectExt = '';

    public function __construct()
    {
        $this->module = static::getClassName(static::class);
        Lang::load($this->module);
        Config::load($this->module);
        $this->templateMain = new Templates\Main();
        $this->templateRow = new Templates\Row();
        $this->templateDisplay = new Templates\Display();
        $this->linkInternal = new InternalLink(Config::getPath());
        $this->linkExternal = new ExternalLink(Config::getPath());
        $this->libFiles = FilesHelper::get(Config::getPath()->getDocumentRoot() . Config::getPath()->getPathToSystemRoot());
    }

    public function process(): void
    {
        $this->path = $this->pathLookup();
        $this->dir = $this->linkInternal->userContent($this->path);
        $this->dirList = new DirectoryListing($this->inputs);
        $this->dirList
            ->setPath($this->dir) # use dir path
            ->setOrderDesc('desc' == $this->getFromParam('order', ''))
            ->setUsableCallback([$this, 'isUsable'])
        ;

        if ($this->dir) {
            $this->preselectExt = $this->getFromParam('ext', '');
            $this->dirList->process();
        }
    }

    protected function pathLookup(): string
    {
        return isset($this->params['path'])
            ? Stuff::arrayToPath(Stuff::linkToArray($this->params['path']))
            : Config::getPath()->getPath() ; # use dir path
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
        if (!empty($this->preselectExt) && ($ext != $this->preselectExt)) {
            return false;
        }

        $denyTypes = Config::get($this->module, 'deny_types', []);
        foreach ($denyTypes as $denyType) {
            if ($ext == strtolower($denyType)) {
                return false;
            }
        }
        return true;
    }

    public function output(): AOutput
    {
        $renderStyle = $this->getFromParam('style', Config::get($this->module, 'style'));
        $rows = (int)$this->getFromParam('row', Config::get($this->module, 'rows'));
        $columns = (int)$this->getFromParam('col', Config::get($this->module, 'cols'));

        $this->templateDisplay->setTemplateName($renderStyle);
        # re-count cols
        if (!empty($this->templateDisplay->getStyles()[$renderStyle])) {
            $rows = $columns * $rows;
            $columns = 1;
        }

        $filesChunks = $this->dirList->getFilesChunked($rows, $columns);
        $out = new Html();

        if (empty($filesChunks)) {
            return $out;
        }

        $lines = [];
        foreach ($filesChunks as $files) {
            $cols = [];
            foreach ($files as $file) {
                $cols[] = $this->templateDisplay->reset()->setData(
                    $this->getIcon($file),
                    $this->getLink($file),
                    $this->getThumb($file),
                    $this->getName($file),
                    $this->getDetails($file, $renderStyle),
                    $this->getInfo($file)
                )->render();
            }
            $lines[] = $this->templateRow->reset()->setData(implode('', $cols))->render();
        }

        $this->templateMain->reset()->setData(implode('', $lines), $this->dirList->getPaging());
        return $out->setContent($this->templateMain->render());
    }

    protected function getLink(string $file): string
    {
        $ext = Stuff::fileExt($file);
        if (in_array($ext, ['htm','html','xhtml','xhtm','htx','htt','php'])) { // create links for normally accessible pages
            return $this->linkExternal->linkVariant($this->path . IPaths::SPLITTER_SLASH . $file);
        } else { // rest of the world must pass as file passed through external link
            return $this->linkExternal->linkVariant($this->path . IPaths::SPLITTER_SLASH . $file, 'file', true);
        }
    }

    protected function getThumb(string $file): string
    {
        $want = $this->libFiles->getLibThumb()->getPath($this->path . DIRECTORY_SEPARATOR . $file);
        return $this->linkInternal->userContent($want)
            ? $this->linkExternal->linkVariant($want, 'Image', true)
            : $this->getIcon($file) ;
    }

    protected function getIcon(string $file): string
    {
        $ext = strtolower(Stuff::fileExt($file));
        return $this->linkExternal->linkModule('Sysimage','images/files/'.$ext.'.png')
            ? $this->linkExternal->linkVariant('files/'.$ext.'.png', 'sysimage', true)
            : $this->linkExternal->linkVariant('files/dummy.png', 'sysimage', true);
    }

    protected function getName(string $file): string
    {
        return $file;
    }

    protected function getDetails(string $file, string $renderStyle): string
    {
        $detailContent = null;
        foreach ($this->detailLink($file) as $v) {
            if (is_null($detailContent) && $v) {
                $detailContent = @file_get_contents($v);
            }
        }
        if (empty($detailContent)) {
            return "";
        }

        $descMaxLen = (int)Config::get($this->module, 'desc_maxlen', 1000);
        if ((strlen($detailContent) > $descMaxLen) && !empty($this->templateDisplay->getStyles()[$renderStyle])) {
            $detailContent = substr($detailContent, 0, ($descMaxLen + 5));
            $detailContent = substr($detailContent, 0, strrpos($detailContent," "));
            $detailContent .= "...";
        };
        return $detailContent;
    }

    protected function detailLink(string $fileName)
    {
        $extendDir = $this->libFiles->getLibDirDesc()->getExtendDir();
        return [
            $this->linkInternal->userContent(implode(DIRECTORY_SEPARATOR, [$this->path, $extendDir->getDescDir(), Stuff::fileBase($fileName) . $extendDir->getDescExt() ])), // files
            $this->linkInternal->userContent(implode(DIRECTORY_SEPARATOR, [$this->path, Stuff::fileBase($fileName), $extendDir->getDescDir(), $extendDir->getDescFile() . $extendDir->getDescExt() ])), // dirs
        ];
    }

    protected function getInfo(string $file): string
    {
        return '';
    }
}
