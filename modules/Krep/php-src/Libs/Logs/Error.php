<?php

namespace KWCMS\modules\Krep\Libs\Logs;


use KWCMS\modules\Krep\Libs;


class Error extends ALogs
{
    protected string $format = "{DATE}|{TIMESTAMP}|{CODE}|{MESSAGE}|{IP}|{TOPIC}|{NAME}|{BROWSER}|{ACCEPT}\r\n";

    protected function fileName(): string
    {
        return 'log_error.txt';
    }

    public function formattedLine(Libs\Add\ServerData $server, string $name, Libs\Shared\PageData $pageData, Libs\ModuleException $ex = null): string
    {
        $o = $this->format;
        $d = time();
        $o = strval(str_replace("{DATE}", date("Y/m/d H:i:s", $d), $o));
        $o = strval(str_replace("{TIMESTAMP}", $d, $o));
        $o = strval(str_replace("{CODE}", $ex ? $ex->getCode() : 0, $o));
        $o = strval(str_replace("{MESSAGE}", $ex ? $ex->getMessage() : '', $o));
        $o = strval(str_replace("{IP}", $server->getIp(), $o));
        $o = strval(str_replace("{TOPIC}", $this->dis($pageData), $o));
        $o = strval(str_replace("{NAME}", $name, $o));
        $o = strval(str_replace("{BROWSER}", $server->getUserAgent(), $o));
        $o = strval(str_replace("{ACCEPT}", $server->getAccept(), $o));

        return $o;
    }
}
