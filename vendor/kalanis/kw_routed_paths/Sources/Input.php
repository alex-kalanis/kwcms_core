<?php

namespace kalanis\kw_routed_paths\Sources;


use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_input\Interfaces\IFiltered;


/**
 * Class Input
 * @package kalanis\kw_routed_paths\Params\Request
 * Input source is Request Uri in IInputs datasource which provides the path data
 * This one is for accessing with url rewrite engines
 * @codeCoverageIgnore access external variable
 */
class Input extends Request
{
    public function __construct(IFiltered $inputs, ?string $virtualDir = null)
    {
        $requestUri = $inputs->getInArray('REQUEST_URI', [IEntry::SOURCE_SERVER, ] );
        $entry = reset($requestUri);
        parent::__construct(
            $entry ? strval($entry) : '',
            $virtualDir
        );
    }
}
