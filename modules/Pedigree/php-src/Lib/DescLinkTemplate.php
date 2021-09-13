<?php

namespace KWCMS\modules\Pedigree\Lib;


use kalanis\kw_langs\Lang;
use kalanis\kw_modules\ATemplate;


/**
 * Class DescLinkTemplate
 * @package KWCMS\modules\Pedigree\Lib
 */
class DescLinkTemplate extends ATemplate
{
    protected $moduleName = 'Pedigree';
    protected $templateName = 'desc_link';

    protected function fillInputs(): void
    {
        $this->addInput('{MORE}', Lang::get('pedigree.page'));
        $this->addInput('{PATH}');
        $this->addInput('{NAME}');
        $this->addInput('{FAMILY}');
    }

    /**
     * @param string $path
     * @param string $name
     * @param string $family
     * @return $this
     */
    public function setData(string $path, string $name, string $family): self
    {
        $this->updateItem('{PATH}', $path);
        $this->updateItem('{NAME}', $name);
        $this->updateItem('{FAMILY}', $family);
        return $this;
    }
}
