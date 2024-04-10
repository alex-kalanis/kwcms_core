<?php

namespace kalanis\kw_address_handler;


/**
 * Class Forward
 * @package kalanis\kw_address_handler
 * Forwarding requests
 */
class Forward
{
    public const KEY_FORWARD = 'fwd';

    protected Handler $urlHandler;

    public function __construct()
    {
        $this->urlHandler = new Handler();
    }

    /**
     * @param string $link
     * @throws HandlerException
     * @return $this
     */
    public function setLink(string $link): self
    {
        return $this->setSource(new Sources\Address($link));
    }

    /**
     * @throws HandlerException
     * @return string
     */
    public function getLink(): string
    {
        return strval($this->urlHandler->getAddress());
    }

    /**
     * @param Sources\Sources $sources
     * @throws HandlerException
     * @return $this
     */
    public function setSource(Sources\Sources $sources): self
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

    /**
     * @param bool $extraRule
     * @codeCoverageIgnore access external call
     */
    public function forward(bool $extraRule = true): void
    {
        if ($extraRule && $this->has()) {
            new Redirect($this->get()); // external
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
