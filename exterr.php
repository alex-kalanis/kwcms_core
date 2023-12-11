<?php

$develVariable = getenv('DEVEL_VARIABLE');
$develVariable = (false !== $develVariable) ? strval($develVariable) : 'okmedcgtivub';

$develValue = getenv('DEVEL_VALUE');
$develValue = (false !== $develValue) ? strval($develValue) : '456852sdfguztvfrnji';

if (isset($_GET[$develVariable]) && $develValue == $_GET[$develVariable]) {
    putenv('DEVEL_ENVIRONMENT=1');
}
