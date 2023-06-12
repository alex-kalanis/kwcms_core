<?php

namespace KWCMS\modules\Transcode\Lib\Variants;


use KWCMS\modules\Transcode\Lib\ASharedArrayVariant;


/**
 * Class Azbuka
 * @package KWCMS\modules\Transcode\Lib\Variants
 */
class Azbuka extends ASharedArrayVariant
{
    public function getSeparatorFrom(): string
    {
        return '';
    }

    public function getSeparatorTo(): string
    {
        return '';
    }

    public function getAllowed(): string
    {
        return '';
    }

    public function getSharedArray(): array
    {
        return [
            'A' => 'А',
            'Ja' => 'Я',
            'B' => 'Б',
            'Č' => 'Ч',
            'D' => 'Д',
            'E' => 'Е',
            'F' => 'Ф',
            'G' => 'Г',
            'I' => 'И',
            'J' => 'Й',
            'K' => 'К',
            'L' => 'Л',
            'M' => 'М',
            'N' => 'Н',
            'O' => 'О',
            'Jo' => 'Ё',
            'P' => 'П',
            'R' => 'Р',
            'S' => 'С',
            'Š' => 'Ш',
            'Šh' => 'Щ',
            'T' => 'Т',
            'C' => 'Ц',
            'U' => 'У',
            'Ju' => 'Ю',
            'V' => 'В',
            'Ch' => 'Х',
            'Z' => 'З',
            'Ž' => 'Ж',
            'Y' => 'Ы',
            'a' => 'а',
            'ja' => 'я',
            'b' => 'б',
            'č' => 'ч',
            'd' => 'д',
            'e' => 'е',
            'f' => 'ф',
            'g' => 'г',
            'i' => 'и',
            'j' => 'й',
            'k' => 'к',
            'l' => 'л',
            'm' => 'м',
            'n' => 'н',
            'o' => 'о',
            'jo' => 'ё',
            'p' => 'п',
            'r' => 'р',
            's' => 'с',
            'š' => 'ш',
            't' => 'т',
            'c' => 'ц',
            'u' => 'у',
            'ju' => 'ю',
            'v' => 'в',
            'ch' => 'х',
            'z' => 'з',
            'ž' => 'ж',
            'y' => 'ы',
            "'" => 'ь',
            "''" => 'Ъ',
        ];
    }
}
