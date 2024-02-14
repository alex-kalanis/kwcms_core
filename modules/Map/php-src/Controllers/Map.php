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
        $template->setIfImage(boolval(intval(strval($this->getFromParam('im', '0')))));
        $template->setTemplateName(strval($this->getFromParam('map', 'osm')));
        $template->setData(
            strval($this->getFromParam('id', '0')),
            strval($this->getFromParam('ns', '50')),
            strval($this->getFromParam('ew', '0')),
            strval($this->getFromParam('lv', '10')),
            strval($this->getFromParam('w', '200')),
            strval($this->getFromParam('h', '200'))
        );
        $out = new Html();
        return $out->setContent($template->render());
    }
}
