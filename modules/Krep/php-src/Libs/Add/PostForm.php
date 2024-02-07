<?php

namespace KWCMS\modules\Krep\Libs\Add;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;


/**
 * Class PostForm
 * @package KWCMS\modules\Krep\Libs\Add
 * @property Controls\Textarea $message
 * @property Controls\Text $username
 * @property Controls\Password $passwd
 * @property Controls\Email $email
 * @property Controls\Text $url
 * @property Controls\Submit $submit
 */
class PostForm extends Form
{
    public function compose(): void
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addTextarea('message', __('post'), null, ['cols'=> 50, 'rows' => 7]);
        $this->addText('username', __('user'), null, ['size' => 15]);
        $this->addPassword('passwd', __('pass'), ['size' => 15]);
        $this->addEmail('email', __('mail'), null, ['size' => 15]);
        $this->addText('url', __('web'), null, ['size' => 15]);
        $this->addSubmit('submit', __('send_post'));
    }
}
