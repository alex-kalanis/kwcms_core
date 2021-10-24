<?php

namespace kalanis\kw_auth_forms\Inputs;


use ArrayAccess;
use kalanis\kw_forms\Controls\Hidden;
use kalanis\kw_forms\Controls\Security\Csrf;
use kalanis\kw_forms\Interfaces\ICsrf;


/**
 * Class AuthCsrf
 * @package kalanis\kw_auth_forms\Inputs
 * Authentication CSRF token
 * No limitations like in usual form CSRF token
 */
class AuthCsrf extends Hidden
{
    /** @var ICsrf */
    protected $csrf = null;
    /** @var string */
    protected $csrfTokenAlias = '';

    public function __construct()
    {
        $this->csrf = $this->getCsrfLib();
    }

    public function getCsrfLib(): ICsrf
    {
        return new Csrf\Simple();
    }

    public function setHidden(string $alias, ArrayAccess &$cookie): parent
    {
        $this->csrf->init($cookie);
        $this->setEntry($alias);
        $this->csrfTokenAlias = "{$alias}SubmitCheck";
        $this->setValue($this->csrf->getToken($this->csrfTokenAlias));
        return $this;
    }

    public function checkToken($incomingValue): bool
    {
        if ($this->csrf->checkToken(strval($incomingValue), $this->csrfTokenAlias)) {
            // token reload
            $this->csrf->removeToken($this->csrfTokenAlias);
            $this->setValue($this->csrf->getToken($this->csrfTokenAlias));
            return true;
        } else {
            return false;
        }
    }
}
