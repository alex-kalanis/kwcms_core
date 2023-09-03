<?php

namespace KWCMS\modules\Chsett\Lib;


use kalanis\kw_accounts\Interfaces\IUser;
use kalanis\kw_langs\Lang;


/**
 * Trait TStatuses
 * @package KWCMS\modules\Chsett\Lib
 * Available statuses
 */
trait TStatuses
{
    protected function statuses(): array
    {
        return [
            '' => Lang::get('chsett.status_unknown'), // IUser::USER_STATUS_UNKNOWN
            IUser::USER_STATUS_DISABLED => Lang::get('chsett.status_disabled'),
            IUser::USER_STATUS_ENABLED => Lang::get('chsett.status_fully_enabled'),
            IUser::USER_STATUS_ONLY_LOGIN => Lang::get('chsett.status_only_login'),
            IUser::USER_STATUS_ONLY_CERT => Lang::get('chsett.status_only_certs'),
        ];
    }
}
