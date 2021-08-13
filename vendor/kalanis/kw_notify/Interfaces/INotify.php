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
    const TARGET_INFO = 'info';
    const TARGET_ERROR = 'error';
    const TARGET_WARNING = 'warning';
    const TARGET_SUCCESS = 'success';

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
     * @return bool
     * @throws NotifyException
     */
    public function check(string $stackName): bool;

    /**
     * Return content in stack
     * @param string $stackName
     * @return string[]
     * @throws NotifyException
     */
    public function get(string $stackName): array;

    /**
     * Clear stack
     * @param string $stackName
     * @throws NotifyException
     */
    public function reset(string $stackName): void;
}
