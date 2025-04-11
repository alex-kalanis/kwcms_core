<?php

namespace KWCMS\modules\Files\Lib;


trait TMultiRule
{
    public function getMulti(string $errorText, ...$args): array
    {
        $rule = new MultiRule();
        $rule->setErrorText($errorText);
        $rule->setAgainstValue(empty($args) ? null : reset($args));
        return [$rule];
    }
}
