<?php

namespace KWCMS\modules\Krep\Libs\Logs;


class Pass extends ALogs
{
    protected function fileName(): string
    {
        return 'log_pass.txt';
    }
}
