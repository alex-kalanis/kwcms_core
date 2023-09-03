<?php

namespace KWCMS\modules\MediaRss\Lib;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class HeadTemplate
 * Link in header
 * @package KWCMS\modules\MediaRss\Lib
 */
class HeadTemplate extends ATemplate
{
    protected $moduleName = 'MediaRss';
    protected $templateName = 'head';

    protected function fillInputs(): void
    {
        $this->addInput('{LINK}', '#');
    }

    public function setData(string $link): self
    {
        $this->updateItem('{LINK}', $link);
        return $this;
    }
}
