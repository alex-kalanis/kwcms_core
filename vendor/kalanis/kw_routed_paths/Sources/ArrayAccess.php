<?php

namespace kalanis\kw_routed_paths\Sources;


/**
 * Class ArrayAccess
 * @package kalanis\kw_routed_paths\Sources
 * Input source is ArrayAccess which provides the path data
 * This one is for accessing with simplified inputs
 */
class ArrayAccess extends Request
{
    /**
     * @param \ArrayAccess<string|int, string|int|float|bool|array<string>> $inputs
     * @param string $key
     * @param string|null $virtualDir
     */
    public function __construct(\ArrayAccess $inputs, string $key = 'REQUEST_URI', ?string $virtualDir = null)
    {
        $requestUrl = '';
        if ($inputs->offsetExists($key)) {
            $requestUrl = strval($inputs->offsetGet($key));
        }
        parent::__construct($requestUrl, $virtualDir);
    }
}
