<?php

namespace KWCMS\modules\Krep\Libs\Logs;


use KWCMS\modules\Krep\Libs;


/**
 * Class Logs
 * @package KWCMS\modules\Krep\Libs\Logs
 * Add record to log
 */
abstract class ALogs
{
    protected string $logsPath = '';

    // datum_citelne|datum_ux|ip|clanek|jmeno|poslana_ip|prohlizec|bere...
    protected string $format = "{DATE}|{TIMESTAMP}|{IP}|{TOPIC}|{NAME}|posted_ip|{BROWSER}|{ACCEPT}\r\n";

    public function __construct(string $logs_path)
    {
        $this->logsPath = $logs_path;
    }

    public function write(string $line): bool
    {
        return boolval(file_put_contents(
            $this->logsPath . $this->fileName(),
            $line,
            FILE_APPEND
        ));
    }

    abstract protected function fileName(): string;

    /**
     * @param Libs\Add\ServerData $server
     * @param string $name
     * @param Libs\Shared\PageData $pageData
     * @throws Libs\ModuleException
     * @return string
     */
    public function formattedLine(Libs\Add\ServerData $server, string $name, Libs\Shared\PageData $pageData): string
    {
        $o = $this->format;
        $d = time();
        $o = strval(str_replace("{DATE}", date("Y/m/d H:i:s", $d), $o));
        $o = strval(str_replace("{TIMESTAMP}", $d, $o));
        $o = strval(str_replace("{IP}", $server->getIp(), $o));
        $o = strval(str_replace("{TOPIC}", $this->dis($pageData), $o));
        $o = strval(str_replace("{NAME}", $name, $o));
        $o = strval(str_replace("{BROWSER}", $server->getUserAgent(), $o));
        $o = strval(str_replace("{ACCEPT}", $server->getAccept(), $o));
        return $o;
    }

    protected function dis(Libs\Shared\PageData $pageData): string
    {
        return sprintf('%s/%s', $pageData->getDiscusNumber(), $pageData->getTopicNumber());
    }
}
