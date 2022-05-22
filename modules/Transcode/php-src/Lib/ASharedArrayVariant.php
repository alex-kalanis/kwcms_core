<?php

namespace KWCMS\modules\Transcode\Lib;


/**
 * Class AVariant
 * @package KWCMS\modules\Transcode\Lib
 */
abstract class ASharedArrayVariant extends AVariant
{
    public function getFrom(): array
    {
        return $this->getSharedArray();
    }

    public function getTo(): array
    {
        return array_flip($this->getSharedArray());
    }

    abstract protected function getSharedArray(): array;
}
