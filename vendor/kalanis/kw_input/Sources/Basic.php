<?php

namespace kalanis\kw_input\Sources;


use kalanis\kw_input\Interfaces;
use kalanis\kw_input\Parsers;


/**
 * Class Basic
 * @package kalanis\kw_input\Sources
 * Source of values to parse and use
 * @codeCoverageIgnore because this is access to php internals
 */
class Basic implements Interfaces\ISource
{
    /** @var string[]|int[]|array<string|int, string|int|bool> */
    protected array $cliArgs = [];
    /** @var string[]|int[]|array<string|int, string|int|bool> */
    protected array $externalArgs = [];

    /**
     * @param array<string|int, string|int> $cliArgs
     * @return $this
     */
    public function setCli(array $cliArgs = []): self
    {
        $this->cliArgs = $cliArgs;
        return $this;
    }

    public function cli(): ?array
    {
        return $this->cliArgs;
    }

    public function get(): ?array
    {
        return $_GET;
    }

    public function post(): ?array
    {
        return $_POST;
    }

    public function files(): ?array
    {
        return $_FILES;
    }

    public function cookie(): ?array
    {
        return $_COOKIE;
    }

    public function session(): ?array
    {
        return PHP_SESSION_ACTIVE == session_status() ? $_SESSION : [];
    }

    public function server(): ?array
    {
        return $_SERVER ? Parsers\FixServer::updateAuth(Parsers\FixServer::updateVars($_SERVER)) : null;
    }

    public function env(): ?array
    {
        return $_ENV;
    }

    public function external(): ?array
    {
        return $this->externalArgs;
    }

    public function inputRawPaths(): ?array
    {
        return ['php://input'];
    }

    /**
     * @param string[]|int[]|array<string|int, string|int|bool> $externalArgs
     * @return $this
     */
    public function setExternal(array $externalArgs = []): self
    {
        $this->externalArgs = $externalArgs;
        return $this;
    }
}
