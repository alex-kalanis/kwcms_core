<?php

namespace kalanis\kw_input\Simplified;


use ArrayIterator;
use kalanis\kw_input\InputException;
use kalanis\kw_input\Interfaces;
use kalanis\kw_input\Traits;
use Traversable;


/**
 * Class CookieAdapter
 * @package kalanis\kw_input\Extras
 * Accessing _COOKIES via ArrayAccess
 * Also set them into the headers
 * "Cannot modify header information - headers already sent"
 */
class CookieAdapter implements Interfaces\IFilteredInputs
{
    use Traits\TFill;

    protected static string $domain = '';
    protected static string $path = '';
    protected static ?int $expire = null;
    protected static bool $secure = false;
    protected static bool $httpOnly = false;
    protected static bool $sameSite = false;
    protected static bool $dieOnSent = false;

    public static function init(
        string $domain,
        string $path,
        ?int $expire = null,
        bool $secure = false,
        bool $httpOnly = false,
        bool $sameSite = false,
        bool $dieOnSent = false
    ): void
    {
        static::$domain = $domain;
        static::$path = $path;
        static::$expire = $expire;
        static::$secure = $secure;
        static::$httpOnly = $httpOnly;
        static::$sameSite = $sameSite;
        static::$dieOnSent = $dieOnSent;
    }

    /**
     * @param string|int $offset
     * @return mixed
     */
    public final function __get($offset)
    {
        return $this->offsetGet($offset);
    }

    /**
     * @param string|int $offset
     * @param mixed|null $value
     * @throws InputException
     */
    public final function __set($offset, $value): void
    {
        $this->offsetSet($offset, $value);
    }

    /**
     * @param string|int $offset
     * @return bool
     */
    public final function __isset($offset): bool
    {
        return $this->offsetExists($offset);
    }

    /**
     * @param string|int $offset
     * @throws InputException
     */
    public final function __unset($offset): void
    {
        $this->offsetUnset($offset);
    }

    public final function offsetExists($offset): bool
    {
        $offset = $this->removeNullBytes(strval($offset));
        return isset($_COOKIE[$offset]) && ('' != $_COOKIE[$offset]);
    }

    #[\ReturnTypeWillChange]
    public final function offsetGet($offset)
    {
        return $_COOKIE[$this->removeNullBytes(strval($offset))];
    }

    /**
     * @param string|int $offset
     * @param mixed|null $value
     * @throws InputException
     */
    public final function offsetSet($offset, $value): void
    {
        $offset = $this->removeNullBytes(strval($offset));
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
        setcookie($offset, strval($value), [
            'expires'  => intval($expire),
            'path'     => strval(static::$path),
            'domain'   => strval(static::$domain),
            'secure'   => boolval(static::$secure),
            'httponly' => boolval(static::$httpOnly),
            'samesite' => static::$sameSite ? 'Strict' : 'Lax', // not in usual config
        ]);
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string|int $offset
     * @throws InputException
     */
    public function offsetUnset($offset): void
    {
        unset($_COOKIE[strval($this->removeNullBytes(strval($offset)))]); // remove immediately
        if (headers_sent()) {
            if (static::$dieOnSent) {
                throw new InputException('Cannot modify header information - headers already sent');
            }
            return;
        }
        // @codeCoverageIgnoreStart
        setcookie(strval($this->removeNullBytes(strval($offset))), '', (time() - 3600), static::$path, static::$domain);
    }
    // @codeCoverageIgnoreEnd

    public function getIterator(): Traversable
    {
        return new ArrayIterator(
            $this->fillFromEntries(Interfaces\IEntry::SOURCE_COOKIE, $_COOKIE),
            ArrayIterator::STD_PROP_LIST | ArrayIterator::ARRAY_AS_PROPS
        );
    }
}
