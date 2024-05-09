<?php

namespace KWCMS\modules\Short\Lib;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_rules\Interfaces\IRules;


/**
 * Class MessageForm
 * @package KWCMS\modules\Short\Lib
 * @property Controls\Text $title
 * @property Controls\Textarea $content
 * @property Controls\Submit $postMessage
 * @property Controls\Reset $clearMessage
 */
class MessageForm extends Form
{
    protected ?ShortMessage $defaultRecord = null;

    public function composeForm(ShortMessage $defaultRecord): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addText('title', Lang::get('short.title'), $defaultRecord->title)
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('warn.must_fill'));
        $this->addTextarea('content', Lang::get('short.message'), $defaultRecord->content, [
            'cols' => 80, 'rows' => 25,
        ])
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('warn.must_fill'));
        $this->addSubmit('postMessage', Lang::get('dashboard.button_set'));
        $this->addReset('clearMessage', Lang::get('dashboard.button_reset'));
        $this->defaultRecord = $defaultRecord;
        return $this;
    }

    public function getDefaultRecord(): ?ShortMessage
    {
        return $this->defaultRecord;
    }
}
