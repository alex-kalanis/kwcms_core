<?php

define('DEVEL_ENVIRONMENT', boolval(intval(strval(getenv('DEVEL_ENVIRONMENT')))));

if (!function_exists('dmp')) {
    function dmp(string $what, ...$other): void
    {
        if (DEVEL_ENVIRONMENT) {
            print_r([$what, $other]);
        }
    }
}

if (!function_exists('dmd')) {
    function dmd(string $what, ...$other): void
    {
        if (DEVEL_ENVIRONMENT) {
            print_r([$what, $other]);
            die();
        }
    }
}
