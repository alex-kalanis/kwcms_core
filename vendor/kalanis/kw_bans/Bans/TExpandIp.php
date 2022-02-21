<?php

namespace kalanis\kw_bans\Bans;


use kalanis\kw_bans\BanException;
use kalanis\kw_bans\Interfaces\IIpTypes;
use kalanis\kw_bans\Interfaces\IKBTranslations;
use kalanis\kw_bans\Ip;


trait TExpandIp
{
    protected $type = IIpTypes::TYPE_NONE;
    protected $blocks = 4;
    protected $delimiter = '.';

    /**
     * @param string $knownIp
     * @return Ip
     * @throws BanException
     */
    public function expandIP(string $knownIp): Ip
    {
        $affectedBits = 0; // aka ignore last bits...
        $subnetMaskPosition = strpos($knownIp, IIpTypes::MASK_SEPARATOR);
        if (false !== $subnetMaskPosition) {
            $affectedBits = substr($knownIp, $subnetMaskPosition + 1);
            $knownIp = substr($knownIp, 0, $subnetMaskPosition);
        }

        $shortenedPart = strpos($knownIp, $this->delimiter . $this->delimiter);
        if (false !== $shortenedPart) {
            $beginPart = 0 == $shortenedPart ? [] : explode($this->delimiter, substr($knownIp, 0, $shortenedPart));
            $cutEnd = $shortenedPart + strlen($this->delimiter . $this->delimiter);
            $endPart = strlen($knownIp) == $cutEnd ? [] : explode($this->delimiter, substr($knownIp, $cutEnd));
            $unfilledBlocks = $this->blocks - (count($beginPart) + count($endPart));
            if ($unfilledBlocks < 0) {
                throw new BanException($this->getLang()->ikbInvalidNumOfBlocksTooMany($knownIp));
            }
            $ipInArray = array_merge($beginPart, array_fill(0, $unfilledBlocks, '0'), $endPart);
        } else {
            $ipInArray = explode($this->delimiter, $knownIp);
        }

        if ($this->blocks != count($ipInArray)) {
            throw new BanException($this->getLang()->ikbInvalidNumOfBlocksAmount($knownIp));
        }

        $ip = clone $this->getBasicIp();
        $ip->setData($this->type, array_map('strval', $ipInArray), $affectedBits);
        return $ip;
    }

    abstract protected function getBasicIp(): Ip;

    abstract protected function getLang(): IKBTranslations;
}
