<?php

namespace KWCMS\modules\Short\Lib;


use kalanis\kw_confs\Config;
use KWCMS\modules\Core\Libs\ATemplate;


/**
 * Class MessageTemplate
 * @package KWCMS\modules\Short\Lib
 */
class MessageTemplate extends ATemplate
{
    protected string $moduleName = 'Short';
    protected string $templateName = 'message';

    protected function fillInputs(): void
    {
        $this->addInput('{SIGN}', Config::get('Short', 'sign', '&gt;'));
        $this->addInput('{DATE}');
        $this->addInput('{TITLE}');
        $this->addInput('{MESSAGE}');
    }

    public function setData(int $date, string $title, string $message, string $format): self
    {
        $this->updateItem('{DATE}', date($format, $date));
        $this->updateItem('{TITLE}', $title);
        $this->updateItem('{MESSAGE}', $message);
        return $this;
    }
}
