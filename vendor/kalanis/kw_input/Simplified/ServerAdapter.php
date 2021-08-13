<?php

namespace kalanis\kw_input\Simplified;


use ArrayAccess;
use kalanis\kw_input\InputException;


/**
 * Class ServerAdapter
 * @package kalanis\kw_input\Extras
 * Accessing _SERVER via ArrayAccess
 * @property string PHP_SELF
 * @property string argv
 * @property string argc
 * @property string GATEWAY_INTERFACE
 * @property string SERVER_ADDR
 * @property string SERVER_NAME
 * @property string SERVER_SOFTWARE
 * @property string SERVER_PROTOCOL
 * @property string REQUEST_METHOD
 * @property string REQUEST_TIME
 * @property string REQUEST_TIME_FLOAT
 * @property string QUERY_STRING
 * @property string DOCUMENT_ROOT
 * @property string HTTP_ACCEPT
 * @property string HTTP_ACCEPT_CHARSET
 * @property string HTTP_ACCEPT_ENCODING
 * @property string HTTP_ACCEPT_LANGUAGE
 * @property string HTTP_CONNECTION
 * @property string HTTP_HOST
 * @property string HTTP_REFERER
 * @property string HTTP_USER_AGENT
 * @property string HTTPS
 * @property string REMOTE_ADDR
 * @property string REMOTE_HOST
 * @property string REMOTE_PORT
 * @property string SCRIPT_FILENAME
 * @property string SERVER_ADMIN
 * @property string SERVER_PORT
 * @property string SERVER_SIGNATURE
 * @property string PATH_TRANSLATED
 * @property string SCRIPT_NAME
 * @property string REQUEST_URI
 * @property string PHP_AUTH_DIGEST
 * @property string PHP_AUTH_USER
 * @property string PHP_AUTH_PW
 * @property string AUTH_TYPE
 * @property string PATH_INFO
 * @property string ORIG_PATH_INFO
 */
class ServerAdapter implements ArrayAccess
{
    use TNullBytes;

    public final function __get($offset)
    {
        return $this->offsetGet($offset);
    }

    public final function __set($offset, $value)
    {
        $this->offsetSet($offset, $value);
        // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    public final function __isset($offset)
    {
        return $this->offsetExists($offset);
    }

    public final function __unset($offset)
    {
        $this->offsetUnset($offset);
        // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    public final function offsetExists($offset)
    {
        return isset($_SERVER[$this->removeNullBytes($offset)]);
    }

    public final function offsetGet($offset)
    {
        return $_SERVER[$this->removeNullBytes($offset)];
    }

    public final function offsetSet($offset, $value)
    {
        throw new InputException('Cannot write into _SERVER variable');
    }

    public final function offsetUnset($offset)
    {
        throw new InputException('Cannot write into _SERVER variable');
    }
}
