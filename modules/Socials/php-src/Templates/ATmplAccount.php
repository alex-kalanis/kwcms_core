<?php

namespace KWCMS\modules\Socials\Templates;


use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class ATmplAccount
 * @package KWCMS\modules\Socials\Templates
 */
abstract class ATmplAccount extends ATemplate
{
    protected string $moduleName = 'Socials';

    protected function fillInputs(): void
    {
        $this->addInput('{LINK}');
    }

    public function setData(string $link): self
    {
        $this->updateItem('{LINK}', $link);
        return $this;
    }
}
