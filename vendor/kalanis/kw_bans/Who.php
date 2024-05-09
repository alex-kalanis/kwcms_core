<?php

namespace kalanis\kw_bans;


class Who
{
    protected string $ipAddress = '';
    protected string $browser = '';
    protected string $name = '';

    public function setData(string $name, string $browser, string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;
        $this->browser = $browser;
        $this->name = $name;
        return $this;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function getBrowser(): string
    {
        return $this->browser;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
