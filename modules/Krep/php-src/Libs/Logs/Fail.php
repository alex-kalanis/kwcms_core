<?php

namespace KWCMS\modules\Krep\Libs\Logs;


class Fail extends ALogs
{
    protected function fileName(): string
    {
        return 'log_fail.txt';
    }
}
