<?php

namespace kalanis\kw_input\Simplified;


use ArrayAccess;
use kalanis\kw_input\InputException;


/**
 * Class CookieAdapter
 * @package kalanis\kw_input\Extras
 * Accessing _COOKIES via ArrayAccess
 * Also set them into the headers
 * "Cannot modify header information - headers already sent"
 */
class CookieAdapter implements ArrayAccess
{
    use TNullBytes;

    protected static $domain = '';
    protected static $path = '';
    protected static $expire = null;
    protected static $secure = false;
    protected static $httpOnly = false;
    protected static $sameSite = false;
    protected static $dieOnSent = false;

    public static function init(string $domain, string $path, ?int $expire = null, bool $secure = false, bool $httpOnly = false, bool $sameSite = false, bool $dieOnSent = false): void
    {
        static::$domain = $domain;
        static::$path = $path;
        static::$expire = $expire;
        static::$secure = $secure;
        static::$httpOnly = $httpOnly;
        static::$sameSite = $sameSite;
        static::$dieOnSent = $dieOnSent;
    }

    public final function __get($offset)
    {
        return $this->offsetGet($offset);
    }

    public final function __set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }

    public final function __isset($offset)
    {
        return $this->offsetExists($offset);
    }

    public final function __unset($offset)
    {
        $this->offsetUnset($offset);
    }

    public final function offsetExists($offset): bool
    {
        $offset = $this->removeNullBytes($offset);
        return isset($_COOKIE[$offset]) && ('' != $_COOKIE[$offset]);
    }

    #[\ReturnTypeWillChange]
    public final function offsetGet($offset)
    {
        return $_COOKIE[$this->removeNullBytes($offset)];
    }

    /**
     * @param string|int $offset
     * @param string|int|float|bool $value
     * @throws InputException
     */
    public final function offsetSet($offset, $value): void
    {
        $offset = $this->removeNullBytes($offset);
        // access immediately
        $_COOKIE[$offset] = $value;

        // now permanent ones
        if (headers_sent()) {
            if (static::$dieOnSent) {
                throw new InputException('Cannot modify header information - headers already sent');
            }
            return;
        }
        // @codeCoverageIgnoreStart
        $expire = is_null(static::$expire) ? null : time() + static::$expire;
        // TODO: php 7.3 required for 'samesite'
        if (73000 < PHP_VERSION_ID) {
            setcookie($offset, $value, [
                'expires'  => $expire,
                'path'     => static::$path,
                'domain'   => static::$domain,
                'secure'   => (bool)static::$secure,
                'httponly' => (bool)static::$httpOnly,
                'samesite' => static::$sameSite ? 'Strict' : 'Lax', // not in usual config
            ]);
        } else {
            setcookie($offset, $value, $expire, static::$path, static::$domain, (bool)static::$secure, (bool)static::$httpOnly);
        }
    }
    // @codeCoverageIgnoreEnd

    /**
     * @param string|int $offset
     * @throws InputException
     */
    public function offsetUnset($offset): void
    {
        unset($_COOKIE[$this->removeNullBytes($offset)]); // remove immediately
        if (headers_sent()) {
            if (static::$dieOnSent) {
                throw new InputException('Cannot modify header information - headers already sent');
            }
            return;
        }
        // @codeCoverageIgnoreStart
        setcookie($this->removeNullBytes($offset), '', (time() - 3600), static::$path, static::$domain);
    }
    // @codeCoverageIgnoreEnd
}
