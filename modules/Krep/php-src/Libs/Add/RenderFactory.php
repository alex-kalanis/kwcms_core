<?php

namespace KWCMS\modules\Krep\Libs\Add;


use KWCMS\modules\Krep\Libs\Interfaces\IContent;


/**
 * Class RenderFactory
 * @package KWCMS\modules\Krep\Libs\Add
 */
class RenderFactory
{
    protected RenderSent $sent;
    protected RenderForm $form;

    public function __construct(RenderSent $sent, RenderForm $form)
    {
        $this->sent = $sent;
        $this->form = $form;
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
