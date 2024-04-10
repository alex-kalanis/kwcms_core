<?php

namespace kalanis\kw_bans\Bans;


class Cut
{
    /** @var array<int, string> */
    protected array $useAddress = [];
    protected string $bitwiseBlock = '';
    protected int $bitsInAffectedPart = 0;

    /**
     * @param array<int, string> $useAddress
     * @param string $bitwiseBlock
     * @param int $bitsInAffectedPart
     * @return $this
     */
    public function setData(array $useAddress, string $bitwiseBlock, int $bitsInAffectedPart): self
    {
        $this->useAddress = $useAddress;
        $this->bitwiseBlock = $bitwiseBlock;
        $this->bitsInAffectedPart = $bitsInAffectedPart;
        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getAddress(): array
    {
        return $this->useAddress;
    }

    public function getBitwiseBlock(): string
    {
        return $this->bitwiseBlock;
    }

    public function getBitsInAffectedPart(): int
    {
        return $this->bitsInAffectedPart;
    }
}
