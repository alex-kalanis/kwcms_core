<?php

namespace KWCMS\modules\Menu\Lib;


use kalanis\kw_forms\Controls\Text;
use kalanis\kw_forms\Interfaces\IOriginalValue;


/**
 * Class InputPosition
 * @package KWCMS\modules\Menu\Lib
 * Form element for positions
 */
class InputPosition extends Text implements IOriginalValue
{
    public function getOriginalValue()
    {
        return $this->originalValue;
    }
}
