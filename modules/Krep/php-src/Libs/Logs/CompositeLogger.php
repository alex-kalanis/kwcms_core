<?php

namespace KWCMS\modules\Krep\Libs\Logs;


use KWCMS\modules\Krep\Libs;


/**
 * Class CompositeLogger
 * @package KWCMS\modules\Krep\Libs\Logs
 */
class CompositeLogger
{
    /** @var Error */
    protected $error = null;
    /** @var Fail */
    protected $fail = null;
    /** @var Pass */
    protected $pass = null;

    public function __construct(Error $error, Fail $fail, Pass $pass)
    {
        $this->error = $error;
        $this->fail = $fail;
        $this->pass = $pass;
    }

    /**
     * @param Libs\Add\ServerData $serverData
     * @param Libs\Shared\PageData $pageData
     * @param string $username
     * @throws Libs\ModuleException
     * @return bool
     */
    public function logPass(Libs\Add\ServerData $serverData, Libs\Shared\PageData $pageData, string $username): bool
    {
        return $this->pass->write($this->pass->formattedLine(
            $serverData,
            $username,
            $pageData
        ));
    }

    /**
     * @param Libs\Add\ServerData $serverData
     * @param Libs\Shared\PageData $pageData
     * @param string $username
     * @throws Libs\ModuleException
     * @return bool
     */
    public function logFail(Libs\Add\ServerData $serverData, Libs\Shared\PageData $pageData, string $username): bool
    {
        return $this->fail->write($this->fail->formattedLine(
            $serverData,
            $username,
            $pageData
        ));
    }

    /**
     * @param Libs\Add\ServerData $serverData
     * @param Libs\Shared\PageData $pageData
     * @param string $username
     * @param Libs\ModuleException $ex
     * @throws Libs\ModuleException
     * @return bool
     */
    public function logError(Libs\Add\ServerData $serverData, Libs\Shared\PageData $pageData, string $username, Libs\ModuleException $ex): bool
    {
        return $this->error->write($this->error->formattedLine(
            $serverData,
            $username,
            $pageData,
            $ex
        ));
    }
}
