<?php

namespace KWCMS\modules\Image;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ATemplate;


/**
 * Class Template
 * @package KWCMS\modules\Image
 */
class Template extends ATemplate
{
    protected $moduleName = 'Image';
    protected $templateName = 'image';

    protected function fillInputs(): void
    {
        $this->addInput('{IMAGE_PATH}');
        $this->addInput('{CLICK_CLOSE}', Lang::get('image.click_close'));
        $this->addInput('{DESCRIPTION}');
        $this->addInput('{LOADED}', Lang::get('image.loaded'));
        $this->addInput('{FOTO_DATA}');
        $this->addInput('{NOSCRIPT}', Lang::get('noscript'));
    }

    public function setData(string $path, string $desc, string $props): self
    {
        $this->updateItem('{IMAGE_PATH}', $path);
        $this->updateItem('{DESCRIPTION}', $desc);
        $this->updateItem('{FOTO_DATA}', $props);
        return $this;
    }
}
