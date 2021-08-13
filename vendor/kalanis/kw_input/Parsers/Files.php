<?php

namespace kalanis\kw_input\Parsers;


/**
 * Class Files
 * @package kalanis\kw_input\Parsers
 * Parse files input
 * Check only names, the rest is usually valid
 */
class Files extends AParser
{
    public function &parseInput(&$input): array
    {
        $trimArray = [];
        foreach ($input as $key => &$posted) {
            $posted['name'] = $this->clear($posted['name']);
            $trimArray[$this->removeNullBytes(trim($key))] = $posted;
        }
        return $trimArray;
    }

    protected function clear($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'clear'], $value);
        } else {
            return $this->removeNullBytes(trim($value));
        }
    }
}
