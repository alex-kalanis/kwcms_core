<?php

namespace KWCMS\modules\Admin\Forms;


use ArrayAccess;
use kalanis\kw_forms\Form;
use kalanis\kw_forms\Controls;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\Support;
use kalanis\kw_rules\Interfaces\IRules;


/**
 * Class LoginForm
 * @package KWCMS\modules\Admin\Forms
 * Admin login form
 * @property Controls\Text $user
 * @property Controls\Password $pass
 * @property Controls\Select $lang
 * @property Controls\Security\Csrf|null $csrf
 * @property Controls\Submit $login
 */
class LoginForm extends Form
{
    /**
     * @param ArrayAccess $cookie
     * @param ArrayAccess $session
     * @return $this
     */
    public function fill(ArrayAccess $cookie, ArrayAccess $session): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addText('user', Lang::get('login.name'))
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('warn.must_fill'));
        $pass = $this->addPassword('pass', Lang::get('login.pass'));
        $pass->addRule(IRules::IS_NOT_EMPTY, Lang::get('warn.must_fill'));
        $this->addSelect('lang', Lang::get('system.use_lang'),
            Support::fillFromArray($session, null),
            [
                'cze' => '&#x010C;esky',
                'eng' => 'English',
                'fra' => 'Francais',
            ],
            ['id' => 'lang_change']); // todo: better way to get languages, not this "hard coded"
//        $this->addCsrf('csrf', $cookie, Lang::get('warn.late'));
        $this->addSubmit('login', Lang::get('login.button'));
        return $this;
    }
}
