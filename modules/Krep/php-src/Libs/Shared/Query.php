<?php

namespace KWCMS\modules\Krep\Libs\Shared;


use KWCMS\modules\Krep\Libs;


/**
 * Class Query
 * @package KWCMS\modules\Krep\Libs\Shared
 */
class Query
{
    /** @var string */
    protected $wantDiscusLink = '/discus/';
    /** @var string */
    protected $wantRedirectLink = '/presmer';

    public function __construct(Libs\Config $config)
    {
        $this->wantDiscusLink = $config->remote_domain . $this->wantDiscusLink;
        $this->wantRedirectLink = $config->remote_domain . $this->wantRedirectLink;
    }

    /**
     * @param string $addr
     * @throws Libs\ModuleException
     * @return string
     */
    public function getContent(string $addr): string
    {
        return $this->remoteRequest($this->checkIsDomain($addr), $this->contextData());
    }

    /**
     * @param string $address
     * @param array<mixed> $contextData
     * @throws Libs\ModuleException
     * @return string
     */
    protected function remoteRequest(string $address, array $contextData): string
    {
        $cnt = @file_get_contents($address, false, stream_context_create($contextData));
        if (false === $cnt) {
            throw new Libs\ModuleException('Bad request content', 503);
        }
        return strval($cnt);
    }

    /**
     * @return array<mixed>
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
     * @param array<mixed> $contextData
     * @return array<mixed>
     */
    public function postToServer(string $address, array $contextData): array
    {
        return get_headers($address, 1, stream_context_create($contextData)); // php 7.1+
    }
}
