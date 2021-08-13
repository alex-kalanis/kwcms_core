<?php

namespace KWCMS\modules\Admin\Forms;


use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_rules\Interfaces\IRules;


/**
 * Class LoginForm
 * @package KWCMS\modules\Admin\Forms
 * Admin login form
 */
class LoginForm extends Form
{
    public function fill(\ArrayAccess $cookie): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addText('user', Lang::get('login.name'))
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('warn.must_fill'));
        $pass = $this->addPassword('pass', Lang::get('login.pass'));
        $pass->addRule(IRules::IS_NOT_EMPTY, Lang::get('warn.must_fill'));
//        $this->addCsrf('csrf', $cookie, Lang::get('warn.late'));
        $this->addSubmit('login', Lang::get('login.button'));
        return $this;
    }
}
