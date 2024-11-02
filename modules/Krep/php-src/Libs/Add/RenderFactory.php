<?php

namespace KWCMS\modules\Krep\Libs\Add;


use KWCMS\modules\Krep\Libs\Interfaces\IContent;


/**
 * Class RenderFactory
 * @package KWCMS\modules\Krep\Libs\Add
 */
class RenderFactory
{
    public function __construct(
        protected readonly RenderSent $sent,
        protected readonly RenderForm $form,
    )
    {
    }

    public function whichContent(?PostForm $form): IContent
    {
        if (empty($form)) {
            return $this->sent; // message sent.
        } else {
            return $this->form->setForm($form); // empty form to fill
        }
    }
}
