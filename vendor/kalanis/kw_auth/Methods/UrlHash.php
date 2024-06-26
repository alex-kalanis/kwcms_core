<?php

namespace kalanis\kw_auth\Methods;


use kalanis\kw_accounts\Interfaces\IAuthCert;
use kalanis\kw_address_handler\Handler;
use kalanis\kw_auth\Traits\TStamp;


/**
 * Class UrlHash
 * @package kalanis\kw_auth\AuthMethods
 * Authenticate via hashed values
 *
 * query:
 * //dummy/u:whoami/?pass=asdf123ghjk456&timestamp=123456&digest=poiuztrewq
 *
 * makes following call:
 * hash($algorithm = <md5 | sha256 | ...> , $key = 'mnbvcx987' . $data = '//dummy/u:whoami/?pass=asdf123ghjk456&timestamp=123456&salt=789' , $signature = 'poiuztrewq'
 *
 * - it removed digest value and added locally stored salt
 */
class UrlHash extends AMethods
{
    use TStamp;

    public const INPUT_NAME = 'name';
    public const INPUT_NAME2 = 'user';
    public const INPUT_STAMP = 'timestamp';
    public const INPUT_DIGEST = 'digest';
    public const INPUT_SALT = 'salt';

    protected IAuthCert $certAuthenticator;
    protected Handler $uriHandler;
    protected string $algorithm = '';

    /**
     * @param IAuthCert $authenticator
     * @param AMethods|null $nextOne
     * @param Handler $uriHandler
     * @param string $algorithm for hash function
     * @link https://php.net/manual/en/function.hash.php
     */
    public function __construct(IAuthCert $authenticator, ?AMethods $nextOne, Handler $uriHandler, string $algorithm)
    {
        parent::__construct($authenticator, $nextOne);
        $this->certAuthenticator = $authenticator;
        $this->uriHandler = $uriHandler;
        $this->algorithm = $algorithm;
    }

    public function process(\ArrayAccess $credentials): void
    {
        $name = $credentials->offsetExists(static::INPUT_NAME) ? strval($credentials->offsetGet(static::INPUT_NAME)) : '' ;
        $name = $credentials->offsetExists(static::INPUT_NAME2) ? strval($credentials->offsetGet(static::INPUT_NAME2) ): $name ;
        $stamp = $credentials->offsetExists(static::INPUT_STAMP) ? intval(strval($credentials->offsetGet(static::INPUT_STAMP))) : 0 ;

        $wantedUser = $this->certAuthenticator->getDataOnly(strval($name));
        $wantedCert = $this->certAuthenticator->getCertData(strval($name));
        if ($wantedUser && $wantedCert && !empty($stamp) && $this->checkStamp($stamp)) {
            // now we have private salt from our storage, so it's time to check it

            // digest out, salt in
            $digest = strval($this->uriHandler->getParams()->offsetGet(static::INPUT_DIGEST));
            $this->uriHandler->getParams()->offsetUnset(static::INPUT_DIGEST);
            $this->uriHandler->getParams()->offsetSet(static::INPUT_SALT, $wantedCert->getSalt());
            $data = strval($this->uriHandler->getAddress());

            // verify
            if (hash($this->algorithm, $wantedCert->getPubKey() . $data) == $digest) {
                // OK
                $this->loggedUser = $wantedUser;
            }
        }
    }

    public function remove(): void
    {
    }
}
