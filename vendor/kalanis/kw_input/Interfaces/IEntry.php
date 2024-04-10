<?php

namespace kalanis\kw_input\Interfaces;


/**
 * Interface IEntry
 * @package kalanis\kw_input\Interfaces
 * Entry interface - this will be shared across the projects
 */
interface IEntry
{
    public const SOURCE_CLI = 'cli';
    public const SOURCE_GET = 'get';
    public const SOURCE_POST = 'post';
    public const SOURCE_FILES = 'files';
    public const SOURCE_COOKIE = 'cookie';
    public const SOURCE_SESSION = 'session';
    public const SOURCE_SERVER = 'server';
    public const SOURCE_ENV = 'environment';
    public const SOURCE_EXTERNAL = 'external';
    public const SOURCE_JSON = 'json';
    public const SOURCE_XML = 'xml';
    public const SOURCE_RAW = 'raw';

    /**
     * Return source of entry
     * @return string
     */
    public function getSource(): string;

    /**
     * Return key of entry
     * @return string
     */
    public function getKey(): string;

    /**
     * Return value of entry
     * It could be anything - string, boolean, array - depends on source
     * @return string
     */
    public function getValue();
}
