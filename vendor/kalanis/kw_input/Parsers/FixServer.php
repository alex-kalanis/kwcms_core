<?php

namespace kalanis\kw_input\Parsers;


/**
 * Class FixServer
 * @package kalanis\kw_input\Parsers
 * Fixes `$_SERVER` variables for various setups.
 * Got from Wordpress
 */
class FixServer
{
    /**
     * @param array<string|int, string|int|bool|null> $in
     * @return array<string|int, string|int|bool|null>
     */
    public static function updateVars(array $in): array
    {
        $default_server_values = array(
            'SERVER_SOFTWARE' => '',
            'REQUEST_URI' => '',
            'PATH_TRANSLATED' => '',
        );

        $in = array_merge($default_server_values, $in);

        // Fix for IIS when running with PHP ISAPI.
        if (
            empty($in['REQUEST_URI'])
            || ('cgi-fcgi' !== PHP_SAPI && preg_match('/^Microsoft-IIS\//', strval($in['SERVER_SOFTWARE'])))
        ) {

            if (isset($in['HTTP_X_ORIGINAL_URL'])) {
                // IIS Mod-Rewrite.
                $in['REQUEST_URI'] = $in['HTTP_X_ORIGINAL_URL'];
            } elseif (isset($in['HTTP_X_REWRITE_URL'])) {
                // IIS Isapi_Rewrite.
                $in['REQUEST_URI'] = $in['HTTP_X_REWRITE_URL'];
            } else {
                // Use ORIG_PATH_INFO if there is no PATH_INFO.
                if (!isset($in['PATH_INFO']) && isset($in['ORIG_PATH_INFO'])) {
                    $in['PATH_INFO'] = $in['ORIG_PATH_INFO'];
                }

                // Some IIS + PHP configurations put the script-name in the path-info (no need to append it twice).
                if (isset($in['PATH_INFO'])) {
                    if (isset($in['SCRIPT_NAME'])) {
                        if ($in['PATH_INFO'] === $in['SCRIPT_NAME']) {
                            $in['REQUEST_URI'] = $in['PATH_INFO'];
                        } else {
                            $in['REQUEST_URI'] = $in['SCRIPT_NAME'] . $in['PATH_INFO'];
                        }
                    } else {
                        $in['REQUEST_URI'] = $in['PATH_INFO'];
                    }
                }

                // Append the query string if it exists and isn't null.
                if (!empty($in['QUERY_STRING'])) {
                    $in['REQUEST_URI'] .= '?' . $in['QUERY_STRING'];
                }
            }
        }

        // Fix for PHP as CGI hosts that set SCRIPT_FILENAME to something ending in php.cgi for all requests.
        if (isset($in['SCRIPT_FILENAME']) && str_ends_with(strval($in['SCRIPT_FILENAME']), 'php.cgi')) {
            $in['SCRIPT_FILENAME'] = $in['PATH_TRANSLATED'];
        }

        // Fix for Dreamhost and other PHP as CGI hosts.
        if (isset($in['SCRIPT_NAME']) && str_contains(strval($in['SCRIPT_NAME']), 'php.cgi')) {
            unset($in['PATH_INFO']);
        }

        // Fix empty PHP_SELF.
        if (empty($in['PHP_SELF'])) {
            $in['PHP_SELF'] = preg_replace('/(\?.*)?$/', '', strval($in['REQUEST_URI']));
        }

        return $in;
    }

    /**
     * @param array<string|int, string|int|bool|null> $in
     * @return array<string|int, string|int|bool|null>
     */
    public static function updateAuth(array $in): array
    {
        // If we don't have anything to pull from, return early.
        if (!isset($in['HTTP_AUTHORIZATION']) && !isset($in['REDIRECT_HTTP_AUTHORIZATION'])) {
            return $in;
        }

        // If either PHP_AUTH key is already set, do nothing.
        if (isset($in['PHP_AUTH_USER']) || isset($in['PHP_AUTH_PW'])) {
            return $in;
        }

        // From our prior conditional, one of these must be set.
        $header = isset($in['HTTP_AUTHORIZATION'])
            ? $in['HTTP_AUTHORIZATION']
            : (isset($in['REDIRECT_HTTP_AUTHORIZATION']) ? $in['REDIRECT_HTTP_AUTHORIZATION'] : '');

        // Test to make sure the pattern matches expected.
        if (!preg_match('%^Basic [a-z\d/+]*={0,2}$%i', strval($header))) {
            return $in;
        }

        // Removing `Basic ` the token would start six characters in.
        $token = substr(strval($header), 6);
        $userpass = base64_decode($token);

        // There must be at least one colon in the string.
        if (!str_contains($userpass, ':')) {
            return $in;
        }

        list($user, $pass) = explode(':', $userpass, 2);

        // Now shove them in the proper keys where we're expecting later on.
        $in['PHP_AUTH_USER'] = $user;
        $in['PHP_AUTH_PW'] = $pass;

        return $in;
    }
}


if (!function_exists('str_contains')) {
    /**
     * Polyfill for `str_contains()` function added in PHP 8.0.
     *
     * Performs a case-sensitive check indicating if needle is
     * contained in haystack.
     *
     * @param string $haystack The string to search in.
     * @param string $needle The substring to search for in the `$haystack`.
     * @return bool True if `$needle` is in `$haystack`, otherwise false.
     * @codeCoverageIgnore php dependency
     */
    function str_contains($haystack, $needle)
    {
        if ('' === $needle) {
            return true;
        }

        return false !== strpos($haystack, $needle);
    }
}

if (!function_exists('str_starts_with')) {
    /**
     * Polyfill for `str_starts_with()` function added in PHP 8.0.
     *
     * Performs a case-sensitive check indicating if
     * the haystack begins with needle.
     *
     * @param string $haystack The string to search in.
     * @param string $needle The substring to search for in the `$haystack`.
     * @return bool True if `$haystack` starts with `$needle`, otherwise false.
     * @codeCoverageIgnore php dependency
     */
    function str_starts_with($haystack, $needle)
    {
        if ('' === $needle) {
            return true;
        }

        return 0 === strpos($haystack, $needle);
    }
}

if (!function_exists('str_ends_with')) {
    /**
     * Polyfill for `str_ends_with()` function added in PHP 8.0.
     *
     * Performs a case-sensitive check indicating if
     * the haystack ends with needle.
     *
     * @param string $haystack The string to search in.
     * @param string $needle The substring to search for in the `$haystack`.
     * @return bool True if `$haystack` ends with `$needle`, otherwise false.
     * @codeCoverageIgnore php dependency
     */
    function str_ends_with($haystack, $needle)
    {
        if ('' === $haystack) {
            return '' === $needle;
        }

        $len = strlen($needle);

        return substr($haystack, -$len, $len) === $needle;
    }
}
