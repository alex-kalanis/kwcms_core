<?php

namespace KWCMS\modules\Styles;


use kalanis\kw_confs\Config;
use kalanis\kw_mime\MimeType;
use kalanis\kw_modules\AModule;
use kalanis\kw_modules\ExternalLink;
use kalanis\kw_modules\Interfaces\ISitePart;
use kalanis\kw_modules\Output;
use kalanis\kw_modules\Processing\Support;
use kalanis\kw_paths\Stuff;
use kalanis\kw_styles\Styles as ExStyles;


/**
 * Class Styles
 * @package KWCMS\modules\Styles
 * Render styles in page
 */
class Styles extends AModule
{
    /** @var MimeType */
    protected $mime = null;
    /** @var StylesTemplate */
    protected $template = null;
    /** @var ExternalLink */
    protected $libExtLink = '';

    public function __construct()
    {
        $this->mime = new MimeType(true);
        $this->template = new StylesTemplate();
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
        $out = new Output\Html();
        if (empty($this->params['path']) || '' == $this->params['path']) {
            $content = [];
            foreach (ExStyles::getAll() as $module => $scripts) {
                foreach ($scripts as $script) {
                    $moduleName = Support::normalizeModuleName($module);
                    $content[] = $this->template->reset()->setData(
                        $this->libExtLink->linkVariant($moduleName . '/' . $script, 'styles', true, false)
                    )->render();
                }
            }
            $out->setContent(implode('', $content));
        } else {
            $out->setContent($this->template->setData(
                $this->libExtLink->linkVariant($this->params['path'], 'styles', true, false)
            )->render());
        }
        return $out;
    }

    public function outContent(): Output\AOutput
    {
        $modulePath = Stuff::linkToArray($this->params['path']);
        $moduleName = array_shift($modulePath);
        $moduleName = Support::normalizeModuleName($moduleName);
        $content = ExStyles::getFile($moduleName, Stuff::arrayToPath($modulePath));
        if ($content) {
            header("Content-Type: " . $this->mime->mimeByPath('any.css'));
        }
        $out = new Output\Raw();
        $out->setContent($content);
        return $out;
    }
}
