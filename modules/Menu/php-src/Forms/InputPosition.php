<?php

namespace KWCMS\modules\Menu\Forms;


use kalanis\kw_forms\Controls\Text;
use kalanis\kw_forms\Interfaces\IOriginalValue;


/**
 * Class InputPosition
 * @package KWCMS\modules\Menu\Forms
 * Form element for positions
 */
class InputPosition extends Text implements IOriginalValue
{
    public function getOriginalValue()
    {
        return $this->originalValue;
    }
}
