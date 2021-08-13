<?php

namespace kalanis\kw_rules\Rules\File;


use kalanis\kw_rules\Interfaces\IValidateFile;
use kalanis\kw_rules\Exceptions\RuleException;


/**
 * Class FileMaxSize
 * @package kalanis\kw_rules\Rules\File
 * Check if input file has correct size
 */
class FileMaxSize extends AFileRule
{
    public function checkValue($againstValue)
    {
        return $this->fileSizeString2size($againstValue);
    }

    public function validate(IValidateFile $entry): void
    {
        if ($entry->getSize() > $this->againstValue) {
            throw new RuleException($this->errorText);
        }
    }

    protected function fileSizeString2size(string $string): int
    {
        $size = intval($string);
        $posK = stripos($string, 'k');
        $posM = stripos($string, 'm');
        $posG = stripos($string, 'g');
        if (false !== $posK) {
            list($value, $foo) = explode(substr($string, $posK, 1), $string);
            $value = floatval($value);
            $size = intval($value * 1024);
        } elseif (false !== $posM) {
            list($value, $foo) = explode(substr($string, $posM, 1), $string);
            $value = floatval($value);
            $size = intval($value * 1024 * 1024);
        } elseif (false !== $posG) {
            list($value, $foo) = explode(substr($string, $posG, 1), $string);
            $value = floatval($value);
            $size = intval($value * 1024 * 1024 * 1024);
        }
        return $size;
    }
}
