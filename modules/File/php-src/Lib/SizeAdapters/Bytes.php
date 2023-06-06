<?php

namespace KWCMS\modules\File\Lib\SizeAdapters;


/**
 * Class Bytes
 * @package KWCMS\modules\File\Lib\SizeAdapters
 */
class Bytes extends AAdapter
{
    public function fillFileDetails(int $fileSize, int $stepBy): void
    {
        $this->seek
            ->setData($fileSize - 1)
            ->setLimits(0, $fileSize - 1)
            ->setStepBy($stepBy)
        ;
    }

    public function getUsableLength(): int
    {
        return ($this->seek->getEnd() - $this->seek->getStart() + 1);
    }

    public function getMaxLength(): int
    {
        return $this->seek->getMax() + 1;
    }

    public function contentRange(): ?string
    {
        return sprintf(
            'Content-Range: bytes %d-%d/%d',
            $this->seek->getStart(),
            $this->seek->getEnd(),
            $this->seek->getMax() + 1
        );
    }

    public function canContinue(int $cur): bool
    {
        return $cur <= $this->seek->getEnd();
    }

    public function readLength(int $cur): int
    {
        return min($this->seek->getStepBy(), ($this->seek->getEnd() - $cur) + 1);
    }
}
