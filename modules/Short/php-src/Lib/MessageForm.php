<?php

namespace KWCMS\modules\Short\Lib;


use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_rules\Interfaces\IRules;
use kalanis\kw_short\ShortMessage;


/**
 * Class MessageForm
 * @package KWCMS\modules\Short\Lib
 */
class MessageForm extends Form
{
    protected $defaultRecord = null;

    public function composeForm(ShortMessage $defaultRecord): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addText('title', Lang::get('short.title'), $defaultRecord->title)
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('warn.must_fill'));
        $this->addTextarea('content', Lang::get('short.message'), $defaultRecord->content, [
            'cols' => 60, 'rows' => 5,
        ])
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('warn.must_fill'));
        $this->addSubmit('postMessage', 'OK');
        $this->defaultRecord = $defaultRecord;
        return $this;
    }

    public function getDefaultRecord(): ?ShortMessage
    {
        return $this->defaultRecord;
    }
}
