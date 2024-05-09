<?php

namespace kalanis\kw_notify;


use kalanis\kw_notify\Interfaces\INotify;


/**
 * Class Notification
 * @package kalanis\kw_notify
 * Notifications
 */
class Notification
{
    protected static ?INotify $storage = null;

    /**
     * @param INotify $storage
     */
    public static function init(INotify $storage): void
    {
        static::$storage = $storage;
    }

    /**
     * @param string $message
     * @throws NotifyException
     */
    public static function addInfo(string $message): void
    {
        static::getNotify()->add(INotify::TARGET_INFO, $message);
    }

    /**
     * @param string $message
     * @throws NotifyException
     */
    public static function addError(string $message): void
    {
        static::getNotify()->add(INotify::TARGET_ERROR, $message);
    }

    /**
     * @param string $message
     * @throws NotifyException
     */
    public static function addWarning(string $message): void
    {
        static::getNotify()->add(INotify::TARGET_WARNING, $message);
    }

    /**
     * @param string $message
     * @throws NotifyException
     */
    public static function addSuccess(string $message): void
    {
        static::getNotify()->add(INotify::TARGET_SUCCESS, $message);
    }

    /**
     * @throws NotifyException
     * @return INotify
     */
    public static function getNotify(): INotify
    {
        if (empty(static::$storage)) {
            throw new NotifyException('You must set notification library first!');
        }
        return static::$storage;
    }
}
