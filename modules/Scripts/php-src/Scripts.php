<?php

namespace KWCMS\modules\Scripts;


use kalanis\kw_confs\Config;
use kalanis\kw_mime\MimeType;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_modules\Interfaces\ISitePart;
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
    /** @var ExternalLink */
    protected $libExtLink = '';

    public function __construct()
    {
        $this->mime = new MimeType(true);
        $this->template = new ScriptsTemplate();
        $this->libExtLink = new ExternalLink(Config::getPath(), false, false);
    }

    public function process(): void
    {
    }

    public function output(): Output\AOutput
    {
        return ($this->params[ISitePart::KEY_LEVEL] == ISitePart::SITE_LAYOUT) ? $this->outLayout() : $this->outContent() ;
    }

    public function outLayout(): Output\AOutput
    {
        $content = [];
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
}
