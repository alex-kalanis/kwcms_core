<?php

namespace KWCMS\modules\Pedigree\Lib;


use kalanis\kw_langs\Lang;
use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class ExtLinkTemplate
 * @package KWCMS\modules\Pedigree\Lib
 */
class ExtLinkTemplate extends ATemplate
{
    protected string $moduleName = 'Pedigree';
    protected string $templateName = 'ext_link';

    protected function fillInputs(): void
    {
        $this->addInput('{TRANS}', Lang::get('pedigree.page'));
        $this->addInput('{PATH}');
        $this->addInput('{IMAGE}');
    }

    /**
     * @param string $path
     * @param string $image
     * @return $this
     */
    public function setData(string $path, string $image): self
    {
        $this->updateItem('{PATH}', $path);
        $this->updateItem('{IMAGE}', $image);
        return $this;
    }
}
