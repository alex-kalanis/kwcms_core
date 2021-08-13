<?php

namespace kalanis\kw_auth\Methods;


use kalanis\kw_address_handler\Handler;
use kalanis\kw_auth\Interfaces\IAuthCert;


/**
 * Class Hash
 * @package kalanis\kw_auth\AuthMethods
 * Authenticate via hashed values
 * @codeCoverageIgnore because access external content
 * - public on server, private on client whom manage the site
 *
 * query:
 * //dummy/u:whoami/?pass=asdf123ghjk456&timestamp=123456&digest=poiuztrewq
 *
 * makes following call:
 * hash($algorithm = <md5 | sha256 | ...> , $data = '/dummy/u:whoami/?pass=asdf123ghjk456&timestamp=123456&salt=789' ) == $signature = 'poiuztrewq'
 *
 * - it removed digest value and added locally stored salt
 */
class Hash extends AMethods
{
    /** @var IAuthCert */
    protected $authenticator;
    /** @var Handler */
    protected $uriHandler = null;
    /** @var string */
    protected $algorithm = '';

    /**
     * Hash constructor.
     * @param IAuthCert $authenticator
     * @param AMethods|null $nextOne
     * @param Handler $uriHandler
     * @param string $algorithm for hash function
     * @link https://php.net/manual/en/function.hash.php
     */
    public function __construct(IAuthCert $authenticator, ?AMethods $nextOne, Handler $uriHandler, string $algorithm)
    {
        parent::__construct($authenticator, $nextOne);
        $this->uriHandler = $uriHandler;
        $this->algorithm = $algorithm;
    }

    public function process(\ArrayAccess $credentials): void
    {
        $name = $credentials->offsetExists('user') ? $credentials->offsetGet('user') : '' ;
        $stamp = $credentials->offsetExists('timestamp') ? $credentials->offsetGet('timestamp') : 0 ;
        $wantedUser = $this->authenticator->getCertData((string)$name);
        if ($wantedUser && !empty($stamp)) { // @todo: check timestamp for range
            // now we have private salt from our storage, so it's time to check it

            // digest out, salt in
            $digest = $this->uriHandler->getParams()->offsetGet('digest');
            $this->uriHandler->getParams()->offsetUnset('digest');
            $this->uriHandler->getParams()->offsetSet('salt', $wantedUser->getPubSalt());
            $data = $this->uriHandler->getAddress();

            // verify
            if (hash($this->algorithm, (string)$data) == (string)$digest) {
                // OK
                $this->loggedUser = $wantedUser;
            }
        }
    }

    public function remove(): void
    {
    }
}
