<?php

namespace kalanis\kw_input\Interfaces;


/**
 * Interface ISource
 * @package kalanis\kw_input\Interfaces
 * Source of values to parse
 */
interface ISource
{
    /**
     * Return content of variables sent as params from CLI
     * @return array<string|int, string|int>|null
     */
    public function cli(): ?array;

    /**
     * Return content of variables sent as params in URI
     * @return array<string|int, string|int|bool|string[]|int[]>|null
     */
    public function get(): ?array;

    /**
     * Return content of variables sent in body
     * @return array<string|int, string|int|bool|string[]|int[]>|null
     */
    public function post(): ?array;

    /**
     * Return content of variables which represents the uploaded files
     * @return array<string|int, array<string, string>|array<string, array<string, string>>>|null
     */
    public function files(): ?array;

    /**
     * Return content of cookie variables
     * @return array<string|int, string|int|bool|string[]|int[]>|null
     */
    public function cookie(): ?array;

    /**
     * Return content of session variables
     * @return array<string|int, string|int|bool|string[]|int[]>|null
     */
    public function session(): ?array;

    /**
     * Return content of server variables
     * @return array<string|int, string|int|bool|null>|null
     */
    public function server(): ?array;

    /**
     * Return content of environment variables
     * @return array<string|int, string|int|bool|string[]>|null
     */
    public function env(): ?array;

    /**
     * Return paths for reading raw input
     * @return string[]|null
     */
    public function inputRawPaths(): ?array;

    /**
     * Return content of user-set external variables
     * @return string[]|int[]|array<string|int, mixed|null>|null
     */
    public function external(): ?array;
}
