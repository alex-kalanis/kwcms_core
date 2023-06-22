<?php

namespace kalanis\kw_bans\Bans;


use kalanis\kw_bans\Interfaces\IKBTranslations;
use kalanis\kw_bans\Ip;
use kalanis\kw_bans\Sources\ASources;
use kalanis\kw_bans\Traits;


abstract class AIP extends ABan
{
    use Traits\TExpandIp;

    /** @var Ip[] */
    protected $knownIps = [];
    /** @var Ip */
    protected $searchIp = null;
    /** @var int */
    protected $bitsInBlock = 4;

    public function __construct(ASources $source, ?IKBTranslations $lang = null)
    {
        $this->setIKbLang($lang);
        $this->setBasicIp(new Ip());
        $this->knownIps = array_map(function ($row) {
            return $this->expandIP($row);
        }, $source->getRecords());
    }

    public function setLookedFor(string $lookedFor): void
    {
        $this->searchIp = $this->expandIP($lookedFor);
    }

    protected function matched(): array
    {
        return array_filter($this->knownIps, [$this, 'comparedByAddress']);
    }

    public function comparedByAddress(Ip $ipAddress): bool
    {
        // compare only parts unaffected by mask, then special bitwise compare for partially affected, cut the rest
        $knownToCompare = $this->cutPositions($ipAddress->getAddress(), $ipAddress->getAffectedBits());
        $searchToCompare = $this->cutPositions($this->searchIp->getAddress(), $ipAddress->getAffectedBits());

//print_r(['cutted', $knownToCompare, $searchToCompare, $leftKnownToBitwiseCompare, $leftSearchToBitwiseCompare, $bitsInAffectedPart]);
        // now compare only relevant portions
        foreach ($knownToCompare->getAddress() as $position => $segment) {
            $forCompare = strval($searchToCompare->getAddress()[$position]);
            if ($segment == $forCompare) {
                continue;
            }
            if ($this->compareAsString($segment, $forCompare)) {
                continue;
            }
            return false;
        }

//print_r(['cmpaddr', $bitsInAffectedPart, $leftKnownToBitwiseCompare, $leftSearchToBitwiseCompare]);
        if (!empty($searchToCompare->getBitsInAffectedPart())) {
            // compare bitwise the last segment
            if (!$this->compareAsBinary($knownToCompare, $searchToCompare)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<int, string> $address
     * @param int $affectedBits
     * @return Cut
     */
    protected function cutPositions(array $address, int $affectedBits): Cut
    {
        $bitsInAffectedPart = $affectedBits % $this->bitsInBlock;
        $affectedBlocks = intval(ceil($affectedBits / $this->bitsInBlock));

        $useAddress = array_slice($address, 0, $this->blocks - $affectedBlocks);
        $bitwiseBlock = $bitsInAffectedPart ? $address[count($useAddress)] : '';

//print_r(['cut', $address, $useAddress, $affectedBits, $bitwiseBlock, $bitsInAffectedPart]);
        return (new Cut())->setData($useAddress, $bitwiseBlock, $bitsInAffectedPart);
    }

    protected function compareAsString(string $known, string $tested): bool
    {
        // direct for *
        if ('*' == $known[0]) {
            return true;
        }
        if (strlen($known) != strlen($tested)) {
            return false;
        }
        // through for ?
        for ($i = 0; $i < strlen($known); $i++) {
            $pos = strval($known[$i]);
            if (('?' != $pos) && ('*' != $pos) && (strval($tested[$i]) != $pos)) {
                return false;
            }
        }
        return true;
    }

    protected function compareAsBinary(Cut $knownToCompare, Cut $searchToCompare): bool
    {
        $testingBinary = str_pad(decbin($this->toNumber($searchToCompare->getBitwiseBlock())), $this->bitsInBlock, '0', STR_PAD_LEFT);
        $knownBinary = str_pad(decbin($this->toNumber($knownToCompare->getBitwiseBlock())), $this->bitsInBlock, '0', STR_PAD_LEFT);
//print_r(['cmpbin', $testingBinary, $knownBinary, $cutBits]);
        if (0 < $searchToCompare->getBitsInAffectedPart()) {
            $testingBinary = substr($testingBinary, 0, ((-1) * $searchToCompare->getBitsInAffectedPart()));
            $knownBinary = substr($knownBinary, 0, ((-1) * $searchToCompare->getBitsInAffectedPart()));
        }
//print_r(['cmpbincut', $testingBinary, $knownBinary, $cutBits]);
        return $testingBinary == $knownBinary;
    }

    abstract protected function toNumber(string $value): int;
}
