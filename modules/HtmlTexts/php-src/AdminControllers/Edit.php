<?php

namespace KWCMS\modules\HtmlTexts\AdminControllers;


use kalanis\kw_address_handler\Redirect;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_scripts\Scripts;
use kalanis\kw_styles\Styles;
use KWCMS\modules\Texts;
use KWCMS\modules\HtmlTexts\Lib;


/**
 * Class Edit
 * @package KWCMS\modules\HtmlTexts\AdminControllers
 * Site's text content - edit correct file
 */
class Edit extends Texts\AdminControllers\Edit
{
    use Lib\TModuleTemplate;

    protected function getParams(): Texts\Lib\Params
    {
        return new Lib\Params();
    }

    protected function targetPreview(): string
    {
        return 'html-texts/preview';
    }

    public function outHtml(): Output\AOutput
    {
        if ($this->error) {
            Notification::addError($this->error->getMessage());
            if ($this->error instanceof Texts\TextsException) {
                new Redirect($this->links->linkVariant($this->targetDone()));
            }
            $this->error = null;
        }
        Scripts::want('html-texts', 'wysiwyg.js');
        Scripts::want('html-texts', 'wysiwyg-color.js');
//        Scripts::want('html-texts', 'wysiwyg-popup.js');
//        Scripts::want('html-texts', 'wysiwyg-settings.js');
        Styles::want('html-texts', 'wysiwyg.css');
        $out = new Output\Html();
        $page = new Lib\EditTemplate();
        if ($this->isProcessed) {
            Notification::addSuccess(Lang::get('texts.file_saved'));
        }
        try {
            $page->setData($this->editFileForm);
            return $out->setContent($this->outModuleTemplate($page->render()));
        } catch (FormsException $ex) {
            $this->error = $ex;
        }
        return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
    }

    protected function targetDone(): string
    {
        return 'html-texts/dashboard';
    }
}
