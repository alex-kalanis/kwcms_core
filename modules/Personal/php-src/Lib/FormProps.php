<?php

namespace KWCMS\modules\Personal\Lib;


use kalanis\kw_accounts\Interfaces\IUser;
use kalanis\kw_accounts\Interfaces\IUserCert;
use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_langs\Lang;
use kalanis\kw_rules\Interfaces\IRules;


/**
 * Class FormProps
 * @package KWCMS\modules\Personal\Lib
 * Edit properties of single user
 * @property Controls\Text $loginName
 * @property Controls\Text $displayName
 * @property Controls\Textarea $pubKey
 * @property Controls\Text $pubSalt
 * @property Controls\Submit $saveDesc
 * @property Controls\Reset $resetDesc
 */
class FormProps extends Form
{
    public function composeForm(IUser $user): self
    {
        $this->setMethod(IEntry::SOURCE_POST);
        $this->addText('loginName', Lang::get('personal.login_name'), $user->getAuthName())
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('personal.login_empty'));
        $this->addText('displayName', Lang::get('personal.display_name'), $user->getDisplayName())
            ->addRule(IRules::IS_NOT_EMPTY, Lang::get('personal.display_empty'));
        $this->addSubmit('saveProp', Lang::get('dashboard.button_set'));
        $this->addReset('resetProp', Lang::get('dashboard.button_reset'));
        return $this;
    }

    public function addCerts(IUserCert $user): self
    {
        $this->addTextarea('pubKey', Lang::get('personal.public_key'), $user->getPubKey(), ['rows' => 10, 'cols' => 70]);
        $this->addText('pubSalt', Lang::get('personal.public_salt'), $user->getPubSalt());
        return $this;
    }
}
