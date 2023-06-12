<?php

namespace KWCMS\modules\Transcode\Lib\Variants;


use KWCMS\modules\Transcode\Lib\ASharedArrayVariant;


/**
 * Class Hexa
 * @package KWCMS\modules\Transcode\Lib\Variants
 */
class Hexa extends ASharedArrayVariant
{
    public function getSeparatorFrom(): string
    {
        return ' ';
    }

    public function getSeparatorTo(): string
    {
        return '';
    }

    public function getAllowed(): string
    {
        return '0123456789ABCDEF';
    }

    public function getSharedArray(): array
    {
        return [
            'A' => '41',
            'B' => '42',
            'C' => '43',
            'D' => '44',
            'E' => '45',
            'F' => '46',
            'G' => '47',
            'H' => '48',
            'I' => '49',
            'J' => '4A',
            'K' => '4B',
            'L' => '4C',
            'M' => '4D',
            'N' => '4E',
            'O' => '4F',
            'P' => '50',
            'Q' => '51',
            'R' => '52',
            'S' => '53',
            'T' => '54',
            'U' => '55',
            'V' => '56',
            'W' => '57',
            'X' => '58',
            'Y' => '59',
            'Z' => '5A',
            'a' => '61',
            'b' => '62',
            'c' => '63',
            'd' => '64',
            'e' => '65',
            'f' => '66',
            'g' => '67',
            'h' => '68',
            'i' => '69',
            'j' => '6A',
            'k' => '6B',
            'l' => '6C',
            'm' => '6D',
            'n' => '6E',
            'o' => '6F',
            'p' => '70',
            'q' => '71',
            'r' => '72',
            's' => '73',
            't' => '74',
            'u' => '75',
            'v' => '76',
            'w' => '77',
            'x' => '78',
            'y' => '79',
            'z' => '7A',
            '0' => '30',
            '1' => '31',
            '2' => '32',
            '3' => '33',
            '4' => '34',
            '5' => '35',
            '6' => '36',
            '7' => '37',
            '8' => '38',
            '9' => '39',
            '.' => '2E',
            ',' => '2C',
            ';' => '3B',
            '!' => '21',
            '?' => '3F',
            ':' => '3A',
            '-' => '2D',
            '=' => '3D',
            '"' => '22',
            '/' => '2F',
            '(' => '28',
            ')' => '29',
            '@' => '40',
            ' ' => '20',
        ];
    }
}
