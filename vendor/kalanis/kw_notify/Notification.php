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
    /** @var INotify|null */
    protected static $storage = null;

    /**
     * @param INotify $storage
     */
    public static function init(INotify $storage)
    {
        static::$storage = $storage;
    }

    public static function getNotify(): ?INotify
    {
        return static::$storage;
    }

    public static function addInfo(string $message): void
    {
        static::$storage->add(INotify::TARGET_INFO, $message);
    }

    public static function addError(string $message): void
    {
        static::$storage->add(INotify::TARGET_ERROR, $message);
    }

    public static function addWarning(string $message): void
    {
        static::$storage->add(INotify::TARGET_WARNING, $message);
    }

    public static function addSuccess(string $message): void
    {
        static::$storage->add(INotify::TARGET_SUCCESS, $message);
    }
}
