<?php

namespace kalanis\kw_auth\Methods;


use ArrayAccess;
use kalanis\kw_address_handler\Handler;
use kalanis\kw_auth\Interfaces\IAuthCert;


/**
 * Class HttpCerts
 * @package kalanis\kw_auth\AuthMethods
 * Authenticate via http certificates
 * - public on server, private on client who manage the site
 */
class HttpCerts extends AMethods
{
    const INPUT_NAME = 'PHP_AUTH_USER';
    const INPUT_PASS = 'PHP_AUTH_DIGEST';
    const INPUT_SALT = 'salt';

    /** @var IAuthCert */
    protected $authenticator;
    /** @var Handler */
    protected $uriHandler = null;
    /** @var ArrayAccess */
    protected $server = null;

    public function __construct(IAuthCert $authenticator, ?AMethods $nextOne, Handler $uriHandler, ArrayAccess $server)
    {
        parent::__construct($authenticator, $nextOne);
        $this->uriHandler = $uriHandler;
        $this->server = $server;
    }

    public function process(\ArrayAccess $credentials): void
    {
        $name = $this->server->offsetExists(static::INPUT_NAME) ? strval($this->server->offsetGet(static::INPUT_NAME)) : '' ;
        $digest = $this->server->offsetExists(static::INPUT_PASS) ? strval($this->server->offsetGet(static::INPUT_PASS)) : '' ;
        $wantedUser = $this->authenticator->getCertData((string)$name);
        if ($wantedUser && $digest) {
            // now we have public key and salt from our storage, so it's time to check it

            // salt in
            $this->uriHandler->getParams()->offsetSet(static::INPUT_SALT, $wantedUser->getPubSalt());
            $data = $this->uriHandler->getAddress();

            // verify
            $result = openssl_verify((string)$data, base64_decode(rawurldecode($digest)), $wantedUser->getPubKey(), OPENSSL_ALGO_SHA256);
            if (1 === $result) {
                // OK
                $this->loggedUser = $wantedUser;
            }
        }
    }

    /**
     * @codeCoverageIgnore headers
     */
    public function remove(): void
    {
        $this->authNotExists();
    }

    /**
     * @codeCoverageIgnore headers
     */
    public function authNotExists(): void
    {
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: DigestCert');
    }
}
