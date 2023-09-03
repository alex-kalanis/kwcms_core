<?php

namespace kalanis\kw_rules\Rules;


use kalanis\kw_rules\Interfaces\IRuleFactory;
use kalanis\kw_rules\Interfaces\IRules;
use kalanis\kw_rules\Exceptions\RuleException;
use ReflectionClass;
use ReflectionException;


/**
 * Class Factory
 * @package kalanis\kw_rules\Rules
 * Factory for getting rules
 */
class Factory implements IRuleFactory
{
    /** @var array<string, string> */
    protected static $map = [
        IRules::MATCH_ALL              => MatchAll::class,
        IRules::MATCH_ANY              => MatchAny::class,
        IRules::MATCH_ENTRY            => MatchByEntry::class,
        IRules::ALWAYS                 => Always::class,
        IRules::EQUALS                 => Equals::class,
        IRules::NOT_EQUALS             => NotEquals::class,
        IRules::IN_ARRAY               => IsInArray::class,
        IRules::NOT_IN_ARRAY           => IsNotInArray::class,
        IRules::IS_GREATER_THAN        => GreaterThan::class,
        IRules::IS_LOWER_THAN          => LesserThan::class,
        IRules::IS_GREATER_THAN_EQUALS => GreaterEquals::class,
        IRules::IS_LOWER_THAN_EQUALS   => LesserEquals::class,
        IRules::IS_NUMERIC             => IsNumeric::class,
        IRules::IS_STRING              => IsString::class,
        IRules::IS_BOOL                => IsBool::class,
        IRules::MATCHES_PATTERN        => MatchesPattern::class,
        IRules::LENGTH_MIN             => LengthMin::class,
        IRules::LENGTH_MAX             => LengthMax::class,
        IRules::LENGTH_EQUALS          => LengthEquals::class,
        IRules::IN_RANGE               => InRange::class,
        IRules::IN_RANGE_EQUALS        => InRangeEquals::class,
        IRules::NOT_IN_RANGE           => OutRange::class,
        IRules::NOT_IN_RANGE_EQUALS    => OutRangeEquals::class,
        IRules::IS_FILLED              => IsFilled::class,
        IRules::IS_NOT_EMPTY           => IsFilled::class,
        IRules::IS_EMPTY               => IsEmpty::class,
        IRules::SATISFIES_CALLBACK     => ProcessCallback::class,
        IRules::IS_EMAIL               => IsEmail::class,
        IRules::IS_DOMAIN              => IsDomain::class,
        IRules::IS_ACTIVE_DOMAIN       => IsActiveDomain::class,
        IRules::URL_EXISTS             => UrlExists::class,
        IRules::IS_JSON_STRING         => IsJsonString::class,
//        IRules::IS_POST_CODE           => External\IsPostCode::class,  // too many formats for simple check, use regex
//        IRules::IS_TELEPHONE           => External\IsPhone::class,  // too many formats for simple check, use regex
//        IRules::IS_EU_VAT              => External\IsEuVat::class,  // too many formats, needs some library for checking
        IRules::IS_DATE                => External\IsDate::class,  // too many formats, needs some library for checking
        IRules::IS_DATE_REGEX          => External\IsDateRegex::class,  // too many formats, needs some library for checking
        IRules::SAFE_EQUALS_BASIC      => Safe\HashedBasicEquals::class,
        IRules::SAFE_EQUALS_FUNC       => Safe\HashedFuncEquals::class,
        IRules::SAFE_EQUALS_PASS       => Safe\HashedPassEquals::class,
    ];

    /**
     * @param string $ruleName
     * @throws RuleException
     * @return ARule
     */
    public function getRule(string $ruleName): ARule
    {
        if (isset(static::$map[$ruleName])) {
            $rule = static::$map[$ruleName];
            try {
                /** @var class-string $rule */
                $ref = new ReflectionClass($rule);
                $class = $ref->newInstance();
                if ($class instanceof ARule) {
                    return $class;
                }
            } catch (ReflectionException $ex) {
                throw new RuleException($ex->getMessage(), $ex->getCode(), $ex);
            }
        }
        throw new RuleException(sprintf('Unknown rule %s', $ruleName));
    }
}
