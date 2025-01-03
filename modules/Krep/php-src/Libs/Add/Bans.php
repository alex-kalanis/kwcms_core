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
    protected kw\Basic $name;
    protected kw\Basic $browser;
    protected kw\IP4 $ip4;
    protected kw\IP6 $ip6;

    /**
     * @param Libs\Logs\CompositeLogger $logger
     * @param string $bans_path
     * @throws BanException
     */
    public function __construct(
        protected readonly Libs\Logs\CompositeLogger $logger,
        readonly string $bans_path,
    )
    {
        $this->name = new kw\Basic(new File($bans_path . 'bannm.txt'));
        $this->browser = new kw\Basic(new File($bans_path . 'banbw.txt'));
        $this->ip4 = new kw\IP4(new File($bans_path . 'ban4.txt'));
        $this->ip6 = new kw\IP6(new File($bans_path . 'ban6.txt'));
    }

    /**
     * @param Libs\Shared\PageData $pageData
     * @param ServerData $serverData
     * @param string $userName
     * @throws Libs\ModuleException
     * @throws BanException
     * @throws Libs\ModuleException
     * @return void
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
            throw new Libs\ModuleException(__('on_banlist'), 403);
        }
    }

    protected function filterName(string $key): string
    {
        return strtr($key, array(" " => "", "_" => "-", "*" => ""));
    }
}
