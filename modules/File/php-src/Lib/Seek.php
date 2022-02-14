<?php

namespace KWCMS\modules\File\Lib;


/**
 * Class Seek
 * @package KWCMS\modules\File\Lib
 * Users files - seek through file
 */
class Seek
{
    protected $stepBy = 16384; // 1024 * 16
    protected $flushEachStep = false;
    protected $usedRange = false;
    protected $filePath = '';
    protected $max = 0;
    protected $start = 0;
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

    public function setData(string $filePath, int $seekMax): self
    {
        $this->filePath = $filePath;
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

    public function getFilePath(): string
    {
        return $this->filePath;
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
