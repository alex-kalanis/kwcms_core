<?php

namespace kalanis\kw_notify\Interfaces;


use kalanis\kw_notify\NotifyException;


/**
 * Interface INotify
 * @package kalanis\kw_storage\Interfaces
 * Format content into and from storage
 */
interface INotify
{
    public const TARGET_INFO = 'info';
    public const TARGET_ERROR = 'error';
    public const TARGET_WARNING = 'warning';
    public const TARGET_SUCCESS = 'success';

    /**
     * Add content to stack
     * @param string $stackName
     * @param string $message
     * @throws NotifyException
     */
    public function add(string $stackName, string $message): void;

    /**
     * Check if stack exists
     * @param string $stackName
     * @throws NotifyException
     * @return bool
     */
    public function check(string $stackName): bool;

    /**
     * Return content in stack
     * @param string $stackName
     * @throws NotifyException
     * @return string[]
     */
    public function get(string $stackName): array;

    /**
     * Clear stack
     * @param string $stackName
     * @throws NotifyException
     */
    public function reset(string $stackName): void;
}
