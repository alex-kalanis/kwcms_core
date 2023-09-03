<?php

namespace KWCMS\modules\Map\Controllers;


use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_modules\Output\Html;
use kalanis\kw_templates\TemplateException;
use KWCMS\modules\Core\Libs\AModule;
use KWCMS\modules\Map\MapTemplate;


/**
 * Class Map
 * @package KWCMS\modules\Map\Controllers
 * Map included as module
 */
class Map extends AModule
{
    public function __construct(...$constructParams)
    {
    }

    public function process(): void
    {
    }

    /**
     * @throws TemplateException
     * @return AOutput
     */
    public function output(): AOutput
    {
        $template = new MapTemplate();
        $template->setIfImage(!empty($this->getFromParam('im', '')));
        $template->setTemplateName((string)$this->getFromParam('map', 'osm'));
        $template->setData(
            (string) $this->getFromParam('id', '0'),
            (string) $this->getFromParam('ns', '50'),
            (string) $this->getFromParam('ew', '0'),
            (string) $this->getFromParam('lv', '10'),
            (string) $this->getFromParam('w', '200'),
            (string) $this->getFromParam('h', '200')
        );
        $out = new Html();
        return $out->setContent($template->render());
    }
}
