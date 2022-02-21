<?php

namespace kalanis\kw_bans\Bans;


use kalanis\kw_bans\BanException;
use kalanis\kw_bans\Interfaces\IIpTypes;
use kalanis\kw_bans\Interfaces\IKBTranslations;
use kalanis\kw_bans\Sources;
use kalanis\kw_bans\Translations;


/**
 * Class Factory
 * @package kalanis\kw_bans\Bans
 */
class Factory
{
    const PREG_IP4 = '#[0-9\./\*]+#i';
    const PREG_IP6 = '#[0-9a-f:/\*]+#i';
    const PREG_NAME = '#[\*\?\:;\\//]#i';

    protected $lang = null;

    public function __construct(?IKBTranslations $lang = null)
    {
        $this->lang = $lang ?: new Translations();
    }

    /**
     * @param int $type
     * @param Sources\ASources $sources
     * @return ABan
     * @throws BanException
     */
    public function getBan(int $type, Sources\ASources $sources): ABan
    {
        switch ($type) {
            case IIpTypes::TYPE_NAME:
                return new Clearing($sources, $this->lang);
            case IIpTypes::TYPE_BASIC:
                return new Basic($sources, $this->lang);
            case IIpTypes::TYPE_IP_4:
                return new IP4($sources, $this->lang);
            case IIpTypes::TYPE_IP_6:
                return new IP6($sources, $this->lang);
            case IIpTypes::TYPE_NONE:
            default:
                throw new BanException($this->lang->ikbUnknownType());
        }
    }

    /**
     * @param string|string[]|Sources\ASources $source
     * @return ABan
     * @throws BanException
     * Filtering has been done by check if there is something left after matching
     */
    public function whichType($source): ABan
    {
        $source = $this->determineSource($source);
        if ($this->emptyContent($source)) {
            return $this->getBan(IIpTypes::TYPE_BASIC, $source);
        } elseif ($this->containsIp4($source)) {
            return $this->getBan(IIpTypes::TYPE_IP_4, $source);
        } elseif ($this->containsIp6($source)) {
            return $this->getBan(IIpTypes::TYPE_IP_6, $source);
        } elseif ($this->containsNames($source)) {
            return $this->getBan(IIpTypes::TYPE_NAME, $source);
        } else {
            return $this->getBan(IIpTypes::TYPE_BASIC, $source);
        }
    }

    /**
     * @param string|string[]|Sources\ASources $source
     * @return Sources\ASources
     * @throws BanException
     */
    protected function determineSource($source): Sources\ASources
    {
        if ($source instanceof Sources\ASources) {
            return $source;
        }
        if (is_array($source)) {
            return new Sources\Arrays(array_map('strval', $source));
        }
        if (is_string($source) && is_file($source)) {
            return new Sources\File($source);
        }
        throw new BanException($this->lang->ikbUnknownFormat());
    }

    protected function emptyContent(Sources\ASources $sources): bool
    {
        return empty( $sources->getRecords() );
    }

    protected function containsIp4(Sources\ASources $sources): bool
    {
        return empty( array_filter($sources->getRecords(), [$this, 'checkForNotIp4']) );
    }

    public function checkForNotIp4(string $content): bool
    {
        return !empty(preg_replace(static::PREG_IP4, '', $content));
    }

    protected function containsIp6(Sources\ASources $sources): bool
    {
        return empty( array_filter($sources->getRecords(), [$this, 'checkForNotIp6']) );
    }

    public function checkForNotIp6(string $content): bool
    {
        return !empty(preg_replace(static::PREG_IP6, '', $content));
    }

    protected function containsNames(Sources\ASources $sources): bool
    {
        return empty( array_filter($sources->getRecords(), [$this, 'checkForNames']) );
    }

    /**
     * @param string $content
     * @return bool
     * Names has no asterisk, question mark or slashes
     * Who set them there is an idiot
     */
    public function checkForNames(string $content): bool
    {
        return (bool)preg_match(static::PREG_NAME, $content);
    }
}
