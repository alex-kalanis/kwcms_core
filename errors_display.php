<?php

if (!defined('DEVEL_ENVIRONMENT')) define('DEVEL_ENVIRONMENT', false);

if (DEVEL_ENVIRONMENT) {
    // show errors
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
