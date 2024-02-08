<?php

namespace KWCMS\modules\Transcode\Lib;


use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;


/**
 * Class MessageForm
 * @package KWCMS\modules\Transcode\Lib
 */
class MessageForm extends Form
{
    public function composeForm(): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addTextarea('data', '', null, [
            'cols' => 60, 'rows' => 5, 'id' => 'datacontent',
        ]);
        $this->addSelect('direction', '', null, [
            'reverse' => '&nbsp;&nbsp;From&nbsp;&nbsp;',
            'straight' => '&nbsp;&nbsp;To&nbsp;&nbsp;',
        ]);
        $this->addSubmit('postMessage', '     Transcode...     ');
        return $this;
    }
}
