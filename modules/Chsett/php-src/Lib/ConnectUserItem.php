<?php

namespace KWCMS\modules\Chsett\Lib;


use kalanis\kw_auth\Interfaces\IUser;
use kalanis\kw_auth\Interfaces\IUserCert;
use kalanis\kw_connect\core\Interfaces\IRow;


/**
 * Class ConnectUserItem
 * @package KWCMS\modules\Chsett\Lib
 * Connect single image to table
 */
class ConnectUserItem implements IRow
{
    protected $array;

    public function __construct(IUser $user)
    {
        $this->array = [
            'id' => $user->getAuthId(),
            'login' => $user->getAuthName(),
            'dir' => $user->getDir(),
            'group' => $user->getGroup(),
            'class' => $user->getClass(),
            'name' => $user->getDisplayName(),
            'canCerts' => $user instanceof IUserCert,
        ];
    }

    public function getValue($property)
    {
        return $this->array[$property];
    }

    public function __isset($name)
    {
        return isset($this->array[$name]);
    }
}
