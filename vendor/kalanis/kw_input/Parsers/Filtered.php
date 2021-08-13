<?php

namespace kalanis\kw_input\Parsers;


/**
 * Class Filtered
 * @package kalanis\kw_input\Parsers
 * @codeCoverageIgnore
 */
class Filtered extends AParser
{
    /**
     * @param string|int $input here is simply what input will be read - INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, or INPUT_ENV
     * @param array|null $params
     * @return array parsed input
     * @link https://www.php.net/manual/en/function.filter-input-array.php
     * @link https://www.php.net/manual/en/filter.filters.validate.php
     *
     * Processing is simple - somewhere before load input entries into array define what it should contain
     */
    public function &parseInput(&$input, array $params = null): array
    {
        $filter = filter_input_array($input, empty($params) ? null : $params);
        return $filter;
    }
}
