<?php

namespace kalanis\kw_input\Parsers;


/**
 * Class Json
 * @package kalanis\kw_input\Parsers
 * Parse input from Json data
 * Also accepts multiple params and returns them as array
 * Cannot fail due incompatible data from source - because that incoming data in input really might not be a JSON
 */
class Json extends AParser
{
    public const FLAG_FILE = 'FILE';

    /**
     * @param int[]|string[] $input is path to json content
     * @return array<string|int, string|int|bool>|array<string|int, array<int|string, string|int|bool>>
     */
    public function parseInput(array $input): array
    {
        $target = reset($input);
        if (false === $target) {
            return [];
        }

        $content = @file_get_contents(strval($target));
        if (false === $content) {
            return [];
        }

        $array = @json_decode($content, true);
        if (is_null($array)) {
            return [];
        }

        $clearArray = [];

        foreach ((array) $array as $key => $posted) {
            $clearArray[strval($this->removeNullBytes($key))] = $this->deepClear($posted, 1);
        }
        return $clearArray;
    }

    /**
     * @param mixed $toClear
     * @param int $level
     * @return array<string, string>|string
     */
    protected function deepClear($toClear, int $level)
    {
        if (!is_array($toClear)) {
            return $this->removeNullBytes(strval($toClear));
        }
        $result = [];
        foreach ($toClear as $key => $item) {
            if ((1 === $level) && (static::FLAG_FILE === $key)) {
                $result[static::FLAG_FILE] = $item;
            } else {
                $result[$this->removeNullBytes($key)] = $this->deepClear($item, $level + 1);
            }
        }
        return $result;
    }
}
