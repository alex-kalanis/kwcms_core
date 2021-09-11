<?php

namespace KWCMS\modules\Texts;


use kalanis\kw_address_handler\Redirect;
use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_confs\Config;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\Stuff;
use kalanis\kw_scripts\Scripts;
use kalanis\kw_storage\StorageException;
use kalanis\kw_styles\Styles;
use KWCMS\modules\Admin\Shared;


/**
 * Class Edit
 * @package KWCMS\modules\Texts
 * Site's text content - edit correct file
 */
class Edit extends AAuthModule implements IModuleTitle
{
    use Lib\TTexts;
    use Lib\TModuleTemplate;

    /** @var Lib\EditFileForm|null */
    protected $editFileForm = null;
    /** @var bool */
    protected $isProcessed = false;

    public function __construct()
    {
        $this->initTModuleTemplate(Config::getPath());
        $this->initTTexts(Config::getPath());
        $this->editFileForm = new Lib\EditFileForm('editFileForm');
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->runTTexts($this->inputs, $this->user->getDir());
        $fileName = $this->getFromParam('fileName');
        if (empty($fileName)) {
            $this->error = new TextsException(Lang::get('texts.file_not_sent'));
            return;
        }
        $ext = Stuff::fileExt(Stuff::filename($fileName));
        if (!in_array($ext, $this->getParams()->filteredTypes())) {
            $this->error = new TextsException(Lang::get('texts.file_wrong_type'));
            return;
        }
        $path = Stuff::sanitize($this->userDir->getHomeDir() . $this->getWhereDir() . DIRECTORY_SEPARATOR . $fileName);
        try {
            $content = $this->storage->exists($path) ? $this->storage->get($path) : '{CREATE_NEW_FREE_FILE}';
            $this->editFileForm->composeForm($content, $fileName, $this->links->linkVariant($this->targetPreview()));
            $this->editFileForm->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->editFileForm->process()) {
                $content = strval($this->editFileForm->getValue('content'));
                if (empty($content)) {
                    $content = base64_decode($this->editFileForm->getValue('content_base64'), true);
                    if (false === $content) {
                        throw new TextsException(Lang::get('texts.file_wrong_content'));
                    }
                }
                $this->isProcessed = $this->storage->set($path, $content);
            }
        } catch (TextsException | StorageException | FormsException $ex) {
            $this->error = $ex;
        }
    }

    protected function getParams(): Lib\Params
    {
        return new Lib\Params();
    }

    protected function targetPreview(): string
    {
        return 'texts/preview';
    }

    public function result(): Output\AOutput
    {
        return $this->isJson()
            ? $this->outJson()
            : $this->outHtml();
    }

    public function outHtml(): Output\AOutput
    {
        if ($this->error) {
            Notification::addError($this->error->getMessage());
            if ($this->error instanceof TextsException) {
                new Redirect($this->links->linkVariant($this->targetDone()));
            }
            $this->error = null;
        }
        Scripts::want('Texts', 'preview.js');
        Styles::want('Texts', 'preview.css');
        $out = new Shared\FillHtml($this->user);
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
        return $out->setContent($this->outModuleTemplate($this->error->getMessage()));
    }

    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            $out->setContent([
                'form_result' => intval($this->isProcessed),
                'form_errors' => $this->editFileForm->renderErrorsArray(),
                'content_base64' => base64_encode($this->editFileForm->getValue('content')),
            ]);
            return $out;
        }
    }

    protected function targetDone(): string
    {
        return 'texts/dashboard';
    }

    public function getTitle(): string
    {
        return Lang::get('texts.page') . ' - ' . Lang::get('texts.edit_file');
    }
}
