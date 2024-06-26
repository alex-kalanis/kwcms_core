<?php

namespace kalanis\kw_auth\Methods;


use ArrayAccess;
use kalanis\kw_accounts\Interfaces\IAuthCert;


/**
 * Class HttpDigest
 * @package kalanis\kw_auth\AuthMethods
 * Authenticate via digest http method
 * @link https://serverpilot.io/docs/how-to-perform-http-digest-authentication-with-php/
 * @link https://www.php.net/manual/en/features.http-auth.php
 */
class HttpDigest extends AMethods
{
    public const INPUT_METHOD = 'REQUEST_METHOD';
    public const INPUT_DIGEST = 'PHP_AUTH_DIGEST';

    protected string $realm = 'KWCMS_Http_Digest';
    protected IAuthCert $certAuthenticator;
    /** @var ArrayAccess<string, string|int> */
    protected ArrayAccess $server;

    /**
     * @param IAuthCert $authenticator
     * @param AMethods|null $nextOne
     * @param ArrayAccess<string, string|int> $server
     */
    public function __construct(IAuthCert $authenticator, ?AMethods $nextOne, ArrayAccess $server)
    {
        parent::__construct($authenticator, $nextOne);
        $this->certAuthenticator = $authenticator;
        $this->server = $server;
    }

    public function process(ArrayAccess $credentials): void
    {
        if (!$this->server->offsetExists(static::INPUT_DIGEST) || empty($this->server->offsetGet(static::INPUT_DIGEST))) {
            return;
        }
        $data = $this->httpDigestParse(strval($this->server->offsetGet(static::INPUT_DIGEST)));
        if (!empty($data)) {
            $wantedUser = $this->certAuthenticator->getDataOnly(strval($data['username']));
            $wantedCert = $this->certAuthenticator->getCertData(strval($data['username']));
            if (!$wantedCert || !$wantedUser) {
                return;
            }

            // verify
            $A1 = md5($data['username'] . ':' . $this->realm . ':' . $wantedCert->getPubKey()); // @todo: srsly, pubkey?! have nothing better?
            $A2 = md5($this->server->offsetGet(static::INPUT_METHOD) . ':' . $data['uri']);
            $valid_response = md5($A1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $A2);

            if ($data['response'] == $valid_response) {
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
        header('WWW-Authenticate: Digest realm="' . $this->realm . '",qop="auth",nonce="' . uniqid() . '",opaque="' . md5($this->realm) . '"');
    }

    /**
     * Parse the http auth header
     * @param string $txt
     * @return array<string, string>
     */
    protected function httpDigestParse(string $txt): array
    {
        // protect against missing data
        $needed_parts = ['nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1];
        $data = [];
        $keys = implode('|', array_keys($needed_parts));

        preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]*?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $data[strval($m[1])] = strval($m[3] ?: ( $m[4] ?? '' ));
            unset($needed_parts[$m[1]]);
        }

        // can be and shall be due that unset a few lines above
        return empty($needed_parts) ? $data : [];
    }
}
