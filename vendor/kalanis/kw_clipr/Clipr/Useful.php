<?php

namespace kalanis\kw_clipr\Clipr;


/**
 * Class Useful
 * @package kalanis\kw_clipr\Clipr
 */
class Useful
{
    /**
     * mb_str_pad
     *
     * @param string $input
     * @param int $pad_length
     * @param string $pad_string
     * @param int $pad_type
     * @return string
     * @author Kari "Haprog" Sderholm https://gist.github.com/nebiros/226350
     */
    public static function mb_str_pad(string $input, int $pad_length, string $pad_string = ' ', int $pad_type = STR_PAD_RIGHT): string
    {
        $diff = strlen( $input ) - mb_strlen( $input );
        return str_pad( $input, $pad_length + $diff, $pad_string, $pad_type );
    }
}
