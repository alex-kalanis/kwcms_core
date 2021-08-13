<?php

namespace kalanis\kw_forms\Controls\Security;


use ArrayAccess;
use kalanis\kw_forms\Controls\Hidden;
use kalanis\kw_rules\Interfaces\IRules;


/**
 * Class MultiSend
 * @package kalanis\kw_forms\Controls\Security
 * Hidden entry which adds Multisend check
 * Must be child of hidden due necessity of pre-setting position in render
 * This one did not set another value to compare, on the other way csrf sets new value
 */
class MultiSend extends Hidden
{
    /** @var ArrayAccess */
    protected $cookie = null;

    public function setHidden(string $alias, ArrayAccess &$cookie, string $errorMessage): parent
    {
        $this->cookie = $cookie;
        $this->setEntry($alias);
        $this->setValue(uniqid('multisend', true));
        $this->addCheckToStack(strval($this->getValue()));
        parent::addRule(IRules::SATISFIES_CALLBACK, $errorMessage, [$this, 'checkMulti']);
        return $this;
    }

    public function checkMulti($incomingValue): bool
    {
        return $this->removeExistingCheckFromStack(strval($incomingValue));
    }

    protected function addCheckToStack(string $value): void
    {
        $hashStack = $this->hashStack();
        $hashStack[$value] = 'FORM_SENDED';
        $this->cookie->offsetSet($this->getKey() . 'SubmitCheck', json_encode($hashStack));
    }

    protected function removeExistingCheckFromStack(string $value): bool
    {
        $hashStack = $this->hashStack();
        if (isset($hashStack[$value])) {
            unset($hashStack[$value]);
            $this->cookie->offsetSet($this->getKey() . 'SubmitCheck', json_encode($hashStack));
            return true;
        }
        return false;
    }

    protected function hashStack()
    {
        $hashStack = $this->cookie->offsetExists($this->getKey() . 'SubmitCheck')
            ? json_decode($this->cookie->offsetGet($this->getKey() . 'SubmitCheck'), true)
            : null ;
        if (is_null($hashStack)) {
            $hashStack = [];
        }
        return $hashStack;
    }

    public function addRule(string $ruleName, string $errorText, ...$args): void
    {
        // no additional rules applicable
    }

    public function addRules(iterable $rules = []): void
    {
        // no rules add applicable
    }

    public function removeRules(): void
    {
        // no rules removal applicable
    }

    public function renderErrors($errors): string
    {
        return '';
    }
}
