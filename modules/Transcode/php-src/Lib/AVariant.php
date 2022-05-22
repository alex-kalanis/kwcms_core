<?php

namespace KWCMS\modules\Transcode\Lib;


/**
 * Class AVariant
 * @package KWCMS\modules\Transcode\Lib
 */
abstract class AVariant
{
    abstract public function getSeparator(): string;

    abstract public function getAllowed(): string;

    abstract public function getFrom(): array;

    abstract public function getTo(): array;

    public function specials(): array
    {
        return [];
    }

    public function leftOversFrom(): array
    {
        return [];
    }

    public function leftOversTo(): array
    {
        return [];
    }
}
