<?php

namespace KWCMS\modules\File\Lib\SizeAdapters;


use KWCMS\modules\File\Lib\Seek;


/**
 * Class AAdapter
 * @package KWCMS\modules\File\Lib\SizeAdapters
 */
abstract class AAdapter
{
    /** @var Seek|null */
    protected $seek = null;

    public function __construct()
    {
        $this->seek = new Seek();
    }

    abstract public function fillFileDetails(string $filePath, int $fileSize, int $stepBy): void;

    public function parseRanges(string $range): void
    {
        if (false !== strpos($range, ',')) {
            // multiple ranges could be specified at the same time
            // but for the sake of sanity only serve the first range
            list($range, $extraRangesNotServed) = explode(',', $range, 2);
        }

        // figure out download piece from range (if set)
        list($seekStart, $seekEnd) = explode('-', $range, 2);

        // set start and end based on range (if set), else set defaults
        // also check for invalid ranges.
        $seekEnd = (empty($seekEnd))
            ? $this->seek->getMax()
            : min(abs(intval($seekEnd)), $this->seek->getMax());
        $seekStart = (empty($seekStart) || abs(intval($seekStart))) > $seekEnd
            ? 0
            : max(abs(intval($seekStart)), 0);

        $this->seek->setLimits($seekStart, $seekEnd)->useRange(true);
    }

    public function usedRange(): bool
    {
        return $this->seek->usedRange();
    }

    abstract public function getUsableLength(): int;

    public function onlyPart(int $maxTransferSize): void
    {
        $this->seek
            ->setLimits($this->seek->getStart(), $this->seek->getStart() + $maxTransferSize)
            ->useRange(true)
        ;
    }

    abstract public function getMaxLength(): int;

    public function inAllowedRange(): bool
    {
        return $this->seek->getStart() <= $this->seek->getMax()
            && $this->seek->getEnd() <= $this->seek->getMax();
    }

    abstract public function contentRange(): ?string;

    public function flush(): bool
    {
        return $this->seek->flush();
    }

    abstract public function canContinue(int $cur): bool;

    abstract public function readLength(int $cur): int;

    public function getStepBy(): int
    {
        return $this->seek->getStepBy();
    }

    public function getFilePath(): string
    {
        return $this->seek->getFilePath();
    }

    public function getMax(): int
    {
        return $this->seek->getMax();
    }

    public function getStart(): int
    {
        return $this->seek->getStart();
    }

    public function getEnd(): int
    {
        return $this->seek->getEnd();
    }
}
