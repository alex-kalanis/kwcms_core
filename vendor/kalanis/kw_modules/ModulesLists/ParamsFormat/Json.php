<?php

namespace kalanis\kw_modules\ModulesLists\ParamsFormat;


use kalanis\kw_modules\Interfaces\Lists\File\IParamFormat;


/**
 * Class Json
 * @package kalanis\kw_modules\ModulesLists\ParamsFormat
 */
class Json implements IParamFormat
{
    public function pack(array $data): string
    {
        return strval(json_encode($data));
    }

    public function unpack(string $content): array
    {
        $data = @ json_decode($content, true);
        return (false === $data) ? [] : $data ;
    }
}
