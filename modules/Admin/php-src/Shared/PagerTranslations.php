<?php

namespace KWCMS\modules\Admin\Shared;


use kalanis\kw_langs\Lang;
use kalanis\kw_paging\Interfaces\IPGTranslations;


/**
 * Class PagerLang
 * @package KWCMS\modules\Admin\Shared
 * Translation of pager results
 */
class PagerTranslations implements IPGTranslations
{
    public function kpgShowResults(int $from, int $to, int $max): string
    {
        return Lang::get('pager.results', $from, $to, $max);
    }
}
