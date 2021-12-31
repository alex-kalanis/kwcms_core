<?php

namespace KWCMS\modules\Transcode\Lib\Variants;


use KWCMS\modules\Transcode\Lib\ASharedArrayVariant;


/**
 * Class Morse
 * @package KWCMS\modules\Transcode\Lib\Variants
 */
class Morse extends ASharedArrayVariant
{
    public function getSeparator(): string
    {
        return '/ ';
    }

    public function getAllowed(): string
    {
        return '.-';
    }

    public function getSharedArray(): array
    {
        return [
            'a' => '.-',
            'b' => '-...',
            'c' => '-.-.',
            'd' => '-..',
            'e' => '.',
            'f' => '..-.',
            'g' => '--.',
            'h' => '....',
            'ch' => '----',
            'i' => '..',
            'j' => '.---',
            'k' => '-.-',
            'l' => '.-..',
            'm' => '--',
            'n' => '-.',
            'o' => '---',
            'p' => '.--.',
            'q' => '--.-',
            'r' => '.-.',
            's' => '...',
            't' => '-',
            'u' => '..-',
            'v' => '...-',
            'w' => '.--',
            'x' => '-..-',
            'y' => '-.--',
            'z' => '--..',
            '0' => '-----',
            '1' => '.----',
            '2' => '..---',
            '3' => '...--',
            '4' => '....-',
            '5' => '.....',
            '6' => '-....',
            '7' => '--...',
            '8' => '---..',
            '9' => '----.',
            '.' => '.-.-.-.',
            ',' => '--..--',
            '?' => '..--..',
            '!' => '--..-',
            ';' => '-.-.-.',
            ':' => '---...',
            '(' => '--...',
            ')' => '-.--.-',
            '"' => '.-..-.',
            '-' => '-....-',
            '=' => '-...-',
            '_' => '..--.-',
            '/' => '-..-.',
            '@' => '.--.-.',
            ' ' => '/'
        ];
    }

    public function specials(): array
    {
        return ['*'=>'.', '\\'=>'/', '|'=>'/', ' '=>'', ];
    }

    public function leftOversFrom(): array
    {
        return ["/"=>""];
    }

    public function leftOversTo(): array
    {
        return ["/ /"=>"//"];
    }
}
