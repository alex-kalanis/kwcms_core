<?php

namespace KWCMS\modules\Scripts;


use kalanis\kw_confs\Config;
use kalanis\kw_mime\MimeType;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\InternalLink;
use kalanis\kw_modules\Output;
use kalanis\kw_modules\Processing\Support;
use kalanis\kw_paths\Stuff;
use kalanis\kw_scripts\Scripts as ExScripts;


/**
 * Class Scripts
 * @package KWCMS\modules\Iframe
 * Render scripts in page
 */
class Scripts extends AModule
{
    /** @var MimeType */
    protected $mime = null;
    /** @var ScriptsTemplate */
    protected $template = null;
    /** @var InternalLink */
    protected $libIntLink = '';
    /** @var ExternalLink */
    protected $libExtLink = '';
    /** @var string */
    protected $extPath = '';
    /** @var string */
    protected $dirPath = '';

    public function __construct()
    {
        $this->mime = new MimeType(true);
        $this->template = new ScriptsTemplate();
        $this->libIntLink = new InternalLink(Config::getPath());
        $this->libExtLink = new ExternalLink(Config::getPath(), false, false);
    }

    public function process(): void
    {
        $extPath = $this->getFromParam('path');
        $this->extPath = $extPath;
        $this->dirPath = $this->libIntLink->userContent($extPath);
    }

    public function output(): Output\AOutput
    {
        return ($this->params[ISitePart::KEY_LEVEL] == ISitePart::SITE_LAYOUT) ? $this->outLayout() : $this->outContent() ;
    }

    public function outLayout(): Output\AOutput
    {
        $content = [];
        if ($this->dirPath) {
            foreach ($this->filesInPath() as $style) {
                $content[] = $this->template->reset()->setData(
                    $this->libExtLink->linkVariant($this->extPath . '/' . $style, 'styles', true)
                )->render();
            }
        }
        foreach (ExScripts::getAll() as $module => $scripts) {
            foreach ($scripts as $script) {
                $content[] = $this->template->reset()->setData(
                    $this->libExtLink->linkVariant($module . '/' . $script, 'scripts', true, false)
                )->render();
            }
        }
        $out = new Output\Html();
        return $out->setContent(implode('', $content));
    }

    public function outContent(): Output\AOutput
    {
        $modulePath = Stuff::linkToArray($this->params['path']);
        $moduleName = array_shift($modulePath);
        $moduleName = Support::normalizeModuleName($moduleName);
        $content = ExScripts::getFile($moduleName, Stuff::arrayToPath($modulePath));
        if ($content) {
            header("Content-Type: " . $this->mime->mimeByPath('any.js'));
        }
        $out = new Output\Raw();
        $out->setContent($content);
        return $out;
    }

    protected function filesInPath(): array
    {
        $preList = scandir($this->dirPath);
        $files = array_filter($preList);
        $files = array_filter($files, [$this, 'filterJs']);
        return $files;
    }

    public function filterJs(string $file): bool
    {
        $ext = Stuff::fileExt($file);
        return in_array($ext, ['js']);
    }
}
