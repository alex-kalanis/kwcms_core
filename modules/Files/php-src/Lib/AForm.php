<?php

namespace KWCMS\modules\Files\Lib;


use kalanis\kw_forms\Controls;
use kalanis\kw_forms\Form;
use kalanis\kw_tree_controls\Controls\DirSelect;
use kalanis\kw_tree_controls\Controls\FileRadio;


/**
 * Class AForm
 * @package KWCMS\modules\Files\Lib
 * Process files and dirs in many ways
 * @property Controls\File uploadedFile
 * @property FileRadio fileName
 * @property DirSelect|Controls\Text|Controls\RadioSet targetPath
 * @property Controls\Submit saveFile
 * @property Controls\Reset resetFile
 */
abstract class AForm extends Form
{
}
