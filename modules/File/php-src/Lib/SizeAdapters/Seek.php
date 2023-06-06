<?php

namespace KWCMS\modules\File\Lib\SizeAdapters;


/**
 * Class Seek
 * @package KWCMS\modules\File\Lib\SizeAdapters
 */
class Seek extends AAdapter
{
    public function fillFileDetails(int $fileSize, int $stepBy): void
    {
        $this->seek
            ->setData($fileSize)
            ->setLimits(0, $fileSize)
            ->setStepBy($stepBy)
        ;
    }

    public function getUsableLength(): int
    {
        return ($this->seek->getEnd() - $this->seek->getStart());
    }

    public function getMaxLength(): int
    {
        return $this->seek->getMax();
    }

    public function contentRange(): ?string
    {
        return sprintf(
            'Content-Range: seek %d-%d/%d',
            $this->seek->getStart(),
            $this->seek->getEnd(),
            $this->seek->getMax()
        );
    }

    public function canContinue(int $cur): bool
    {
        return $cur < $this->seek->getEnd();
    }

    public function readLength(int $cur): int
    {
        return min($this->seek->getStepBy(), ($this->seek->getEnd() - $cur));
    }
}
