<?php

namespace kalanis\kw_modules\ModulesLists\ParamsFormat;


use kalanis\kw_modules\Interfaces\Lists\File\IParamFormat;


/**
 * Class Serialize
 * @package kalanis\kw_modules\ModulesLists\ParamsFormat
 */
class Serialize implements IParamFormat
{
    public function pack(array $data): string
    {
        return serialize($data);
    }

    public function unpack(string $content): array
    {
        $data = @ unserialize($content);
        return (false === $data) ? [] : $data ;
    }
}
