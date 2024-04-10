<?php

namespace KWCMS\modules\Krep\Libs\Add;


use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_forums\Interfaces\ITargets;
use KWCMS\modules\Krep\Libs;


class RenderForm implements Libs\Interfaces\IContent
{
    protected Libs\Shared\Links $links;
    /** @var PostForm */
    protected ?PostForm $form = null;

    public function __construct(Libs\Shared\Links $links)
    {
        $this->links = $links;
    }

    public function setForm(PostForm $form): self
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @param Libs\Shared\PageData $pageData
     * @throws Libs\ModuleException
     * @throws RenderException
     * @return string
     */
    public function getContent(Libs\Shared\PageData $pageData): string
    {
        if (empty($this->form)) {
            throw new Libs\ModuleException('Form not set!');
        }

        $addFormTemplate = new Libs\Template("add_reply");
        $addFormTemplate->change('{USER_INPUT}', $this->form->getControl('username')->renderInput());
        $addFormTemplate->change('{MESSAGE_INPUT}', $this->form->getControl('message')->renderInput());
        $addFormTemplate->change('{LINK_INPUT}', $this->form->getControl('url')->renderInput());
        $addFormTemplate->change('{PASS_INPUT}', $this->form->getControl('passwd')->renderInput());
        $addFormTemplate->change('{SUBMIT_INPUT}', $this->form->getControl('submit')->renderInput());
        $addFormTemplate->change('{USER}', __("user"));
        $addFormTemplate->change('{PASS}', __("pass"));
        $addFormTemplate->change('{WEB}', __("web"));
        $addFormTemplate->change('{POST}', __("post"));
        $addFormTemplate->change('{ADD_POST}', __("add_post"));
        $addFormTemplate->change('{HINTS}', __("hints"));
        $addFormTemplate->change('{THEMA_NAME}', $pageData->getDiscusDesc());
        $addFormTemplate->change('{TOPIC_NAME}', $pageData->getLevelDesc());
        $addFormTemplate->change('{CONTINUE}', $this->links->get($pageData, true));

        if (ITargets::FORM_SEND_ALL == $pageData->getShowForm()) {
            $mailTemplate = new Libs\Template("add_reply_mail");
            $mailTemplate->change('{EMAIL_INPUT}', $this->form->getControl('email')->renderInput());
            $mailTemplate->change('{MAIL}', __("mail"));
            $addFormTemplate->change('{MAIL_TEMPLATE}', $mailTemplate->render());
        } else {
            $addFormTemplate->change('{MAIL_TEMPLATE}', __('err_only_for_registered'));
        }
        return $addFormTemplate->render();
    }
}
