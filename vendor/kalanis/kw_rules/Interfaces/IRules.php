<?php

namespace kalanis\kw_rules\Interfaces;


interface IRules
{
    /* Match all subrules, fail if any fails */
    public const MATCH_ALL = 'matchall';
    /* Match any subrule, fail if every one fails */
    public const MATCH_ANY = 'matchany';
    /* Match by entry, fail if subrule or entry itself is not valid */
    public const MATCH_ENTRY = 'matchentry';

    /* Match always - usually kill the rest of processing */
    public const ALWAYS = 'always';
    /* Match when input equals expected value */
    public const EQUALS = 'equals';
    /* Match when input not equals expected value */
    public const NOT_EQUALS = 'nequals';
    /* Match when input is in array of expected values */
    public const IN_ARRAY = 'inarr';
    /* Match when input is not in array expected values */
    public const NOT_IN_ARRAY = 'ninarr';
    /* Check if input is greater than preset value */
    public const IS_GREATER_THAN = 'greater';
    /* Check if input is lower than preset value */
    public const IS_LOWER_THAN = 'lower';
    /* Check if input is greater than or equals preset value */
    public const IS_GREATER_THAN_EQUALS = 'gteq';
    /* Check if input is lower than or equals preset value */
    public const IS_LOWER_THAN_EQUALS = 'lweq';
    /* Check if input is number */
    public const IS_NUMERIC = 'numeric';
    /* Check if input is string */
    public const IS_STRING = 'string';
    /* Check if input is boolean (true, false, 0, 1) */
    public const IS_BOOL = 'bool';
    /* Check if input matches preset pattern in regular expression */
    public const MATCHES_PATTERN = 'match';
    /* Check if input length is longer than preset value */
    public const LENGTH_MIN = 'min';
    /* Check if input length is shorter than preset value */
    public const LENGTH_MAX = 'max';
    /* Check if input length equals preset value */
    public const LENGTH_EQUALS = 'eql';
    /* Check if input is in range of values (x and y) */
    public const IN_RANGE = 'range';
    /* Check if input is in range of values <x and y> */
    public const IN_RANGE_EQUALS = 'rangeequals';
    /* Check if input is not in range of values (x and y) */
    public const NOT_IN_RANGE = 'nrange';
    /* Check if input is not in range of values <x and y> */
    public const NOT_IN_RANGE_EQUALS = 'nrangeeqals';
    /* Check if value is filled with something */
    public const IS_FILLED = 'fill';
    public const IS_NOT_EMPTY = 'nemtpy';
    /* Check if value is considered empty */
    public const IS_EMPTY = 'empty';
    /* Check if value satisfies callback function */
    public const SATISFIES_CALLBACK = 'call';
    /* Check if value is correct email */
    public const IS_EMAIL = 'mail';
    /* Check if value is valid domain */
    public const IS_DOMAIN = 'domain';
    /* Check if input is callable URL (got code 200) */
    public const URL_EXISTS = 'url';
    /* Check if input is active domain callable from the line */
    public const IS_ACTIVE_DOMAIN = 'domainactive';
    /* Check if input is valid JSON string */
    public const IS_JSON_STRING = 'json';

    /// Checks for files ///
    /* Has file been sent */
    public const FILE_EXISTS = 'fileexist';
    /* Has file been sent */
    public const FILE_SENT = 'fileout';
    /* Has file been received */
    public const FILE_RECEIVED = 'filein';
    /* Check file max size */
    public const FILE_MAX_SIZE = 'filesize';
    /* Check if file mime type is in preset array */
    public const FILE_MIMETYPE_IN_LIST = 'filemimelist';
    /* Check if file mime type equals preset one */
    public const FILE_MIMETYPE_EQUALS = 'filemime';
    /* Check if input file is an image */
    public const IS_IMAGE = 'image';
    /* Check if input image has correct size */
    public const IMAGE_DIMENSION_EQUALS = 'imgsizeeq';
    /* Check if input image has size defined in preset list */
    public const IMAGE_DIMENSION_IN_LIST = 'imgsizelist';
    /* Check if input image is not larger than preset values */
    public const IMAGE_MAX_DIMENSION = 'imgmaxsize';
    /* Check if input image is not smaller than preset values */
    public const IMAGE_MIN_DIMENSION = 'imgminsize';

    /// Need external sources ///
    /* Check if input is post code */
    public const IS_POST_CODE = 'postcode';
    /* Check if input is valid phone number */
    public const IS_TELEPHONE = 'phone';
    /* Check if input is correct EU VAT number */
    public const IS_EU_VAT = 'euvat';
    /* Check if input is correct date in expected format */
    public const IS_DATE = 'date';
    /* Check if input is correct date in expected format */
    public const IS_DATE_REGEX = 'rgxdate';

    /// Secured matching - like for passwords ///
    /* Match when hash of input equals hashed expected value */
    public const SAFE_EQUALS_BASIC = 'hbequals';
    /* Match when input equals expected value via direct function */
    public const SAFE_EQUALS_FUNC = 'hfequals';
    /* Match when hashes of input and expected value equals via password check */
    public const SAFE_EQUALS_PASS = 'hpass';
}
