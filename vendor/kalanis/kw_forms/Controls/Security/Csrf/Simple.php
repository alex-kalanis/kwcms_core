<?php

namespace kalanis\kw_forms\Controls\Security\Csrf;


use ArrayAccess;
use kalanis\kw_forms\Interfaces\ICsrf;


/**
 * Class Simple
 * Secure forms by simple tokens
 * @package kalanis\kw_forms\Controls\Security\Csrf
 */
class Simple implements ICsrf
{
    protected ?ArrayAccess $session = null;
    protected int $expire = 3600;

    public function init(ArrayAccess &$session, int $expire = 3600): void
    {
        $this->session = $session;
        $this->expire = $expire;
    }

    public function removeToken(string $codeName): void
    {
        if ($this->getSession()->offsetExists($codeName)) {
            $this->getSession()->offsetUnset($codeName);
        }
    }

    public function getToken(string $codeName): string
    {
        if (!$this->getSession()->offsetExists($codeName)) {
            $this->getSession()->offsetSet($codeName, uniqid('csrf', true));
            $this->getSession()->offsetSet($codeName . '_timer', time() + $this->expire);
        }
        return strval($this->getSession()->offsetGet($codeName));
    }

    public function getExpire(): int
    {
        return $this->expire;
    }

    public function checkToken(string $token, string $codeName): bool
    {
        return $this->getSession()->offsetExists($codeName)
                && $this->getSession()->offsetExists($codeName . '_timer')
                && $this->getSession()->offsetGet($codeName) == $token
                && $this->getSession()->offsetGet($codeName . '_timer') > time()
                ;
    }

    protected function getSession(): ArrayAccess
    {
        if (!empty($this->session)) {
            return $this->session;
        }
        // @codeCoverageIgnoreStart
        // you need to whant session before call that sets the control
        throw new \LogicException('Set the session first!');
        // @codeCoverageIgnoreEnd
    }
}
