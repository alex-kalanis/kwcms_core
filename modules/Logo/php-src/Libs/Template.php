<?php

namespace KWCMS\modules\Logo\Libs;


use kalanis\kw_confs\Config;
use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class Template
 * @package KWCMS\modules\Logo\Libs
 */
class Template extends ATemplate
{
    protected $moduleName = 'Logo';
    protected $templateName = 'logo';

    protected function fillInputs(): void
    {
        $this->addInput('{PATH}');
        $this->addInput('{WIDTH}', Config::get('Logo', 'width'));
        $this->addInput('{HEIGHT}', Config::get('Logo', 'height'));
    }

    public function setData(string $path): self
    {
        $this->updateItem('{PATH}', $path);
        return $this;
    }
}
