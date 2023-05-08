<?php

namespace kalanis\kw_routed_paths\Sources;


/**
 * Class Server
 * @package kalanis\kw_routed_paths\Sources
 * Input source is Request Uri in _SERVER variable
 * This one is for accessing with url rewrite engines
 * @codeCoverageIgnore access external variable
 */
class Server extends Request
{
    public function __construct(?string $virtualDir = null)
    {
        parent::__construct($_SERVER['REQUEST_URI'], $virtualDir);
    }
}
