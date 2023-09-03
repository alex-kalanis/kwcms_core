<?php

namespace kalanis\kw_modules\ModulesLists\ParamsFormat;


use kalanis\kw_modules\Interfaces\Lists\File\IParamFormat;
use kalanis\kw_paths\Stuff;


/**
 * Class Http
 * @package kalanis\kw_modules\ModulesLists\ParamsFormat
 */
class Http implements IParamFormat
{
    public function pack(array $data): string
    {
        return Stuff::arrayIntoHttpString($data);
    }

    public function unpack(string $content): array
    {
        return Stuff::httpStringIntoArray($content);
    }
}
