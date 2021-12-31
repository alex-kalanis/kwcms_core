<?php

namespace KWCMS\modules\Transcode\Templates;


use kalanis\kw_templates\ATemplate;
use kalanis\kw_templates\Template\TInputs;


/**
 * Class MainTemplate
 * @package KWCMS\modules\Transcode\Templates
 */
class MainTemplate extends ATemplate
{
    use TInputs;

    public function loadTemplate(): string
    {
        return '<!DOCTYPE html>
<html><head>
<title>{TITLE}</title>
<meta http-equiv="content-Type" content="text/html; charset={ENCODING}">
<meta name="robots" content="noindex">
<meta name="googlebot" content="nosnippet,noarchive">
<link rel="stylesheet" type="text/css" href="/web/ms:styles/transcode/transcode.css">
<script type="text/JavaScript" src="/web/ms:scripts/transcode/transcode.js"></script>
</head><body>
<h1>{NAME}</h1>
{CONTENT}
</body></html>';
    }
}
