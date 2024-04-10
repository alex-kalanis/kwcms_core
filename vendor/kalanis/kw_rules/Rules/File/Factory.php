<?php

namespace kalanis\kw_rules\Rules\File;


use kalanis\kw_rules\Interfaces\IRuleFactory;
use kalanis\kw_rules\Interfaces\IRules;
use kalanis\kw_rules\Exceptions\RuleException;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\kw_rules\Rules\File
 * Factory for getting rules for files
 */
class Factory implements IRuleFactory
{
    /** @var array<string, class-string<AFileRule>> */
    protected array $map = [
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
        if (isset($this->map[$ruleName])) {
            $rule = $this->map[$ruleName];
            try {
                $ref = new ReflectionClass($rule);
                $class = $ref->newInstance();
                if ($class instanceof AFileRule) {
                    return $class;
                }
            } catch (ReflectionException $ex) {
                throw new RuleException($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
        throw new RuleException(sprintf('Unknown rule %s', $ruleName));
    }
}
