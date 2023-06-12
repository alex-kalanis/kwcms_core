<?php

namespace KWCMS\modules\Transcode\Lib\Variants;


use KWCMS\modules\Transcode\Lib\ASharedArrayVariant;


/**
 * Class Mobile
 * @package KWCMS\modules\Transcode\Lib\Variants
 */
class Mobile extends ASharedArrayVariant
{
    public function getSeparatorFrom(): string
    {
        return '/ ';
    }

    public function getSeparatorTo(): string
    {
        return '';
    }

    public function getAllowed(): string
    {
        return '*#1234567890';
    }

    public function getSharedArray(): array
    {
        return [
            'A' => '#2',
            'B' => '#22',
            'C' => '#222',
            'D' => '#3',
            'E' => '#33',
            'F' => '#333',
            'G' => '#4',
            'H' => '#44',
            'I' => '#444',
            'J' => '#5',
            'K' => '#55',
            'L' => '#555',
            'M' => '#6',
            'N' => '#66',
            'O' => '#666',
            'P' => '#7',
            'Q' => '#77',
            'R' => '#777',
            'S' => '#7777',
            'T' => '#8',
            'U' => '#88',
            'V' => '#888',
            'W' => '#9',
            'X' => '#99',
            'Y' => '#999',
            'Z' => '#9999',
            'a' => '2',
            'b' => '22',
            'c' => '222',
            'd' => '3',
            'e' => '33',
            'f' => '333',
            'g' => '4',
            'h' => '44',
            'i' => '444',
            'j' => '5',
            'k' => '55',
            'l' => '555',
            'm' => '6',
            'n' => '66',
            'o' => '666',
            'p' => '7',
            'q' => '77',
            'r' => '777',
            's' => '7777',
            't' => '8',
            'u' => '88',
            'v' => '888',
            'w' => '9',
            'x' => '99',
            'y' => '999',
            'z' => '9999',
            '0' => '00',
            '1' => '1111111',
            '2' => '2222',
            '3' => '3333',
            '4' => '4444',
            '5' => '5555',
            '6' => '6666',
            '7' => '77777',
            '8' => '8888',
            '9' => '99999',
            '.' => '1',
            ',' => '11',
            '!' => '1111',
            '?' => '111',
            ':' => '1111111111111',
            '-' => '11111111',
            '"' => '111111',
            '/' => '111111111111',
            '(' => '111111111',
            ')' => '1111111111',
            '@' => '11111111111',
            ' ' => '0'
        ];
    }

    public function leftOversFrom(): array
    {
        return ["/ "=>"","//"=>" ","/"=>""];
    }
}
