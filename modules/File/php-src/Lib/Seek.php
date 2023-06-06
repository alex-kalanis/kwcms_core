<?php

namespace KWCMS\modules\File\Lib;


/**
 * Class Seek
 * @package KWCMS\modules\File\Lib
 * Users files - seek through file
 */
class Seek
{
    /** @var int */
    protected $stepBy = 16384; // 1024 * 16
    /** @var bool */
    protected $flushEachStep = false;
    /** @var bool */
    protected $usedRange = false;
    /** @var int */
    protected $max = 0;
    /** @var int */
    protected $start = 0;
    /** @var int */
    protected $end = 0;

    public function useRange(bool $useRange): self
    {
        $this->usedRange = $useRange;
        return $this;
    }

    public function usedRange(): bool
    {
        return $this->usedRange;
    }

    public function flush(): bool
    {
        return $this->flushEachStep;
    }

    public function setData(int $seekMax): self
    {
        $this->max = $seekMax; // (file size - 1) -> ex: size 7344, range 0-7343
        return $this;
    }

    public function setLimits(int $seekStart, int $seekEnd): self
    {
        $this->start = $seekStart;
        $this->end = $seekEnd;
        return $this;
    }

    public function setStepBy(int $stepBy): self
    {
        $this->stepBy = $stepBy;
        return $this;
    }

    public function getStepBy(): int
    {
        return $this->stepBy;
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getEnd(): int
    {
        return $this->end;
    }
}
