<?php

namespace kalanis\kw_address_handler;


/**
 * Class Redirect
 * @package kalanis\kw_address_handler
 * Redirects in project
 * @codeCoverageIgnore access external call
 */
class Redirect
{
    const TARGET_MOVED = 301;
    const TARGET_FOUND = 302;
    const TARGET_TEMPORARY = 307;
    const TARGET_PERMANENT = 308;

    public function __construct(string $redirectTo, int $targetMethod = self::TARGET_MOVED, ?int $step = null)
    {
        if (strncmp('cli', PHP_SAPI, 3) !== 0) {
            if (headers_sent() !== true) {
                if ($step) {
                    header( 'Refresh:' . $step . ';url=' . $this->removeNullBytes($redirectTo) );
                } else {
                    header('Location: ' . $this->removeNullBytes($redirectTo), true, $targetMethod);
                    exit(0);
                }
            }
        }
    }

    protected function removeNullBytes($string, $nullTo = '')
    {
        return str_replace(chr(0), $nullTo, $string);
    }
}
