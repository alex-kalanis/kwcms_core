<?php

namespace kalanis\kw_rules\Rules\File;


use kalanis\kw_rules\Interfaces\IRuleFactory;
use kalanis\kw_rules\Interfaces\IRules;
use kalanis\kw_rules\Exceptions\RuleException;


/**
 * Class Factory
 * @package kalanis\kw_rules\Rules\File
 * Factory for getting rules for files
 */
class Factory implements IRuleFactory
{
    /** @var array<string, string> */
    protected static $map = [
        IRules::FILE_EXISTS             => FileExists::class,
        IRules::FILE_SENT               => FileSent::class,
        IRules::FILE_RECEIVED           => FileReceived::class,
        IRules::FILE_MAX_SIZE           => FileMaxSize::class,
        IRules::FILE_MIMETYPE_EQUALS    => FileMimeEquals::class,
        IRules::FILE_MIMETYPE_IN_LIST   => FileMimeList::class,
        IRules::IS_IMAGE                => ImageIs::class,
        IRules::IMAGE_DIMENSION_EQUALS  => ImageSizeEquals::class,
        IRules::IMAGE_DIMENSION_IN_LIST => ImageSizeList::class,
        IRules::IMAGE_MAX_DIMENSION     => ImageSizeMax::class,
        IRules::IMAGE_MIN_DIMENSION     => ImageSizeMin::class,
    ];

    /**
     * @param string $ruleName
     * @throws RuleException
     * @return AFileRule
     */
    public function getRule(string $ruleName): AFileRule
    {
        if (isset(static::$map[$ruleName])) {
            $rule = static::$map[$ruleName];
            $class = new $rule();
            if ($class instanceof AFileRule) {
                return $class;
            }
        }
        throw new RuleException(sprintf('Unknown rule %s', $ruleName));
    }
}
