<?php

namespace KWCMS\modules\Krep\Libs\Shared;


use KWCMS\modules\Krep\Libs;


/**
 * Class Query
 * @package KWCMS\modules\Krep\Libs\Shared
 * @link https://wiki.php.net/rfc/http-last-response-headers
 */
class Query
{
    protected string $wantDiscusLink = '/discus/';
    protected string $wantRedirectLink = '/presmer';

    public function __construct(
        readonly Libs\Config $config,
    )
    {
        $this->wantDiscusLink = $config->remote_domain . $this->wantDiscusLink;
        $this->wantRedirectLink = $config->remote_domain . $this->wantRedirectLink;
    }

    /**
     * @param string $addr
     * @throws Libs\ModuleException
     * @return ResponseData
     */
    public function getContent(string $addr): ResponseData
    {
        return $this->query($this->checkIsDomain($addr), $this->contextData());
    }

    /**
     * @return array<string, array<string, string|int|bool>>
     */
    protected function contextData(): array
    {
        return [
//            "ssl" => [
////                "verify_peer" => true,
////                "verify_peer_name" => true,
//            ],
        ];
    }

    /**
     * @param string $address
     * @throws Libs\ModuleException
     * @return string
     */
    protected function checkIsDomain(string $address): string
    {
        $beginDiscusLink = substr($address, 0, strlen($this->wantDiscusLink));
        $beginRedirectLink = substr($address, 0, strlen($this->wantRedirectLink));
        if (($this->wantDiscusLink != $beginDiscusLink) && ($this->wantRedirectLink != $beginRedirectLink)) {
            throw new Libs\ModuleException('Bad request link', 503);
        }
        if ($this->wantRedirectLink != $beginRedirectLink) {
            $address = urldecode($address);
        }
        return 'https://' . $address;
    }

    /**
     * @param PageData $pageData
     * @param Libs\Add\ServerData $serverData
     * @param string $message
     * @param string $user
     * @param string $pass
     * @param string $mail
     * @param string $url
     * @throws Libs\ModuleException
     * @return array<mixed>
     */
    public function contextDataForPost(
        PageData $pageData,
        Libs\Add\ServerData $serverData,
        string $message,
        string $user,
        string $pass,
        string $mail,
        string $url
    ) {
        $content = [
            'action' => '',
            'HTTP_REFERER' => sprintf('%s/%s', $pageData->getDiscusNumber(), $pageData->getTopicNumber()),
            'preview' => 0,
            'mobile' => 1,
            'lstmd' => time() - 60,
            'message' => $message,
            'username' => $user,
            'passwd' => $pass,
            'email' => $mail,
            'url' => $url,
            'html_a' => 1,
            'active_links_a' => 1,
            'original_ip' => $serverData->getIp(),
        ];

        // ultra-blba varianta jenom s query
        $content = http_build_query($content);
        $header = [
            'User-Agent: ' . $serverData->getUserAgent(),
            'Accept: ' . $serverData->getAccept(),
            'Accept-Language: ' . $serverData->getAcceptLanguage(),
            'Content-type: application/x-www-form-urlencoded',
            'Content-length: ' . mb_strlen($content),
            'Referer: https://www.k-report.net/discus/messages/' . $pageData->getDiscusNumber() . '/' . $pageData->getTopicNumber() . '.html',
        ];

        return [
            'ssl' => [
//                'verify_peer' => false,
//                'verify_peer_name' => false,
//                'allow_self_signed' => true,
            ],
            'http' => [
                'method' => 'POST',
//                'user_agent' => $serverData->getUserAgent(),
                'header' => implode("\r\n", $header),
                'timeout' => 30,
                'content' => $content,
            ]
        ];
    }

    /**
     * @param string $address
     * @param array<string, array<string, string|int>> $contextData
     * @throws Libs\ModuleException
     * @return ResponseData
     */
    public function postToServer(string $address, array $contextData): ResponseData
    {
        return $this->query($address, $contextData);
    }

    /**
     * @param string $address
     * @param array<string, array<string, string|int|bool>> $contextData
     * @throws Libs\ModuleException
     * @return ResponseData
     * @link https://www.php.net/manual/en/reserved.variables.httpresponseheader.php
     */
    protected function query(string $address, array $contextData): ResponseData
    {
        $cnt = @file_get_contents($address, false, stream_context_create($contextData));
        if (false === $cnt) {
            throw new Libs\ModuleException('Bad request content', 503);
        }
        if (
            function_exists('http_get_last_response_headers')
            && function_exists('http_clear_last_response_headers')
        ) { // 8.4+
            $http_response_header = http_get_last_response_headers();
            http_clear_last_response_headers();
        }
        if (empty($http_response_header)) {
            throw new Libs\ModuleException('Bad response headers', 503);
        }
        return new ResponseData($this->parseHeaders($http_response_header), $cnt);
    }

    /**
     * @param array<string> $headers
     * @return array{
     *     response_code?: int,
     *     last-modified?: string,
     *     accept-ranges?: string,
     *     cache-control?: string,
     *     expires?: string,
     *     content-length?: int,
     *     content-type?: string,
     *     server?: string
     * }
     * @return array<string|int, string|int>
     */
    private function parseHeaders(array $headers): array
    {
        $head = [];
        foreach ($headers as $k => $v) {
            $t = explode(':', $v, 2);
            if (isset($t[1])) {
                $head[trim($t[0])] = trim($t[1]);
            } else {
                $head[] = $v;
                if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $v, $out)) {
                    $head['response_code'] = intval($out[1]);
                }
            }
        }
        return $head;
    }
}
