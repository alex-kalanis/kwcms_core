<?php

namespace kalanis\kw_bans\Bans;


use kalanis\kw_bans\Interfaces\IKBTranslations;
use kalanis\kw_bans\Ip;
use kalanis\kw_bans\Sources\ASources;
use kalanis\kw_bans\Translations;


abstract class AIP extends ABan
{
    use TExpandIp;
    use TLangIp;

    /** @var Ip[] */
    protected $knownIps = [];
    /** @var Ip */
    protected $searchIp = '';

    protected $bitsInBlock = 4;

    public function __construct(ASources $source, ?IKBTranslations $lang = null)
    {
        $this->setLang($lang ?: new Translations());
        $this->setBasicIp(new Ip());
        $this->knownIps = array_map(function ($row) {
            return $this->expandIP($row);
        }, $source->getRecords());
    }

    public function setLookedFor(string $lookedFor): void
    {
        $this->searchIp = $this->expandIP($lookedFor);
    }

    protected function compare(): void
    {
        $this->foundRecords = array_filter($this->knownIps, [$this, 'comparedByAddress']);
//print_r(['recs', $this->foundRecords]);
    }

    public function comparedByAddress(Ip $ipAddress): bool
    {
        // compare only parts unaffected by mask, then special bitwise compare for partially affected, cut the rest
        list($knownToCompare, $leftKnownToBitwiseCompare, $bitsInAffectedPart) = $this->cutPositions($ipAddress->getAddress(), $ipAddress->getAffectedBits());
        list($searchToCompare, $leftSearchToBitwiseCompare, $bitsInAffectedPart) = $this->cutPositions($this->searchIp->getAddress(), $ipAddress->getAffectedBits());

//print_r(['cutted', $knownToCompare, $searchToCompare, $leftKnownToBitwiseCompare, $leftSearchToBitwiseCompare, $bitsInAffectedPart]);
        // now compare only relevant portions
        foreach ($knownToCompare as $position => $segment) {
            $forCompare = strval($searchToCompare[$position]);
            if ($segment == $forCompare) {
                continue;
            }
            if ($this->compareAsString($segment, $forCompare)) {
                continue;
            }
            return false;
        }

//print_r(['cmpaddr', $bitsInAffectedPart, $leftKnownToBitwiseCompare, $leftSearchToBitwiseCompare]);
        if (!empty($bitsInAffectedPart)) {
            // compare bitwise the last segment
            if (!$this->compareAsBinary($leftKnownToBitwiseCompare, $leftSearchToBitwiseCompare, $bitsInAffectedPart)) {
                return false;
            }
        }

        return true;
    }

    protected function cutPositions(array $address, int $affectedBits): array
    {
        $bitsInAffectedPart = $affectedBits % $this->bitsInBlock;
        $affectedBlocks = intval(ceil($affectedBits / $this->bitsInBlock));

        $useAddress = array_slice($address, 0, $this->blocks - $affectedBlocks);
        $bitwiseBlock = $bitsInAffectedPart ? $address[count($useAddress)] : '';

//print_r(['cut', $address, $useAddress, $affectedBits, $bitwiseBlock, $bitsInAffectedPart]);
        return [$useAddress, $bitwiseBlock, $bitsInAffectedPart];
    }

    protected function compareAsString(string $known, string $tested): bool
    {
        // direct for *
        if ("*" == $known[0]) {
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

    protected function compareAsBinary(string $known, string $tested, int $cutBits): bool
    {
        $testingBinary = str_pad(decbin($this->toNumber($tested)), $this->bitsInBlock, '0', STR_PAD_LEFT);
        $knownBinary = str_pad(decbin($this->toNumber($known)), $this->bitsInBlock, '0', STR_PAD_LEFT);
//print_r(['cmpbin', $testingBinary, $knownBinary, $cutBits]);
        if (0 < $cutBits) {
            $testingBinary = substr($testingBinary, 0, ((-1) * $cutBits));
            $knownBinary = substr($knownBinary, 0, ((-1) * $cutBits));
        }
//print_r(['cmpbincut', $testingBinary, $knownBinary, $cutBits]);
        return $testingBinary == $knownBinary;
    }

    abstract protected function toNumber(string $value): int;
}
