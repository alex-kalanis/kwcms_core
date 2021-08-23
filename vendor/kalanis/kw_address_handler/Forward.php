<?php

namespace kalanis\kw_address_handler;


use kalanis\kw_address_handler\Sources\Address;
use kalanis\kw_address_handler\Sources\Sources;


/**
 * Class Forward
 * @package kalanis\kw_address_handler
 * Forwarding requests
 */
class Forward
{
    const KEY_FORWARD = 'fwd';

    /** @var Handler */
    protected $urlHandler;

    public function __construct()
    {
        $this->urlHandler = new Handler();
    }

    public function setLink(string $link): self
    {
        return $this->setSource(new Address($link));
    }

    public function getLink(): string
    {
        return $this->urlHandler->getAddress();
    }

    public function setSource(Sources $sources): self
    {
        $this->urlHandler->setSource($sources);
        return $this;
    }

    public function setForward(string $forward): self
    {
        $urlVariable = new SingleVariable($this->urlHandler->getParams());
        $urlVariable->setVariableName(static::KEY_FORWARD);
        $urlVariable->setVariableValue($forward);
        return $this;
    }

    public function forward(bool $extraRule = true): void
    {
        if ($extraRule && $this->has()) {
            new Redirect($this->get());
        }
    }

    public function has(): bool
    {
        return !empty($this->get());
    }

    public function get(): string
    {
        $urlVariable = new SingleVariable($this->urlHandler->getParams());
        $urlVariable->setVariableName(static::KEY_FORWARD);
        return $urlVariable->getVariableValue();
    }
}
