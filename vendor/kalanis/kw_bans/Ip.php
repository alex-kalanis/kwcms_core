<?php

namespace kalanis\kw_bans;


/**
 * Class Ip
 * @package kalanis\kw_bans\Bans
 * Basic representation of IP address
 * @link https://www.ibm.com/docs/en/ts3500-tape-library?topic=formats-subnet-masks-ipv4-prefixes-ipv6
 */
class Ip
{
    /** @var int */
    protected $type = Interfaces\IIpTypes::TYPE_NONE;
    /** @var array<int, string> */
    protected $address = [];
    /** @var int */
    protected $affectedBits = 0;

    /**
     * @param int $type
     * @param array<int, string> $address
     * @param int $affectedBits
     * @return $this
     */
    public function setData(int $type, array $address, int $affectedBits = 0): self
    {
        $this->type = $type;
        $this->address = $address;
        $this->affectedBits = $affectedBits;
        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return array<int, string>
     */
    public function getAddress(): array
    {
        return $this->address;
    }

    public function getAffectedBits(): int
    {
        return $this->affectedBits;
    }
}
