<?php

namespace KWCMS\modules\Krep\Libs\Add;


use kalanis\kw_bans\BanException;
use kalanis\kw_bans\Bans as kw;
use kalanis\kw_bans\Sources\File;
use KWCMS\modules\Krep\Libs;


/**
 * Class Bans
 * @package KWCMS\modules\Krep\Libs\Add
 */
class Bans
{
    /** @var kw\Basic */
    protected $name = null;
    /** @var kw\Basic */
    protected $browser = null;
    /** @var kw\IP4 */
    protected $ip4 = null;
    /** @var kw\IP6 */
    protected $ip6 = null;
    /** @var Libs\Logs\CompositeLogger */
    protected $logger = null;

    /**
     * @param Libs\Logs\CompositeLogger $logger
     * @param string $bans_path
     * @throws BanException
     */
    public function __construct(Libs\Logs\CompositeLogger $logger, string $bans_path)
    {
        $this->logger = $logger;
        $this->name = new kw\Basic(new File($bans_path . 'bannm.txt'));
        $this->browser = new kw\Basic(new File($bans_path . 'banbw.txt'));
        $this->ip4 = new kw\IP4(new File($bans_path . 'ban4.txt'));
        $this->ip6 = new kw\IP6(new File($bans_path . 'ban6.txt'));
    }

    /**
     * @param Libs\Shared\PageData $pageData
     * @throws Libs\ModuleException
     * @throws BanException
     */
    public function checkBans(Libs\Shared\PageData $pageData, ServerData $serverData, string $userName): void
    {
        $this->name->setLookedFor($this->filterName($userName));
        $this->browser->setLookedFor($serverData->getUserAgent());

        $ip = strpos($serverData->getIp(), '.') ? $this->ip4 : $this->ip6 ;
        $ip->setLookedFor($serverData->getIp());

        if ($this->name->isBanned() || $this->browser->isBanned() || $ip->isBanned()) {
            $this->logger->logFail(
                $serverData,
                $pageData,
                $userName
            );
            throw new Libs\ModuleException('User banned', 403);
        }
    }

    protected function filterName(string $key): string
    {
        return strtr($key, array(" " => "", "_" => "-", "*" => ""));
    }
}
