<?php

namespace KWCMS\modules\Menu;


use kalanis\kw_address_handler\Forward;
use kalanis\kw_address_handler\Sources\ServerRequest;
use kalanis\kw_auth\Interfaces\IAccessClasses;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_menu\Menu\Entry;
use kalanis\kw_menu\MenuException;
use kalanis\kw_modules\AAuthModule;
use kalanis\kw_modules\Interfaces\IModuleTitle;
use kalanis\kw_modules\Output;
use kalanis\kw_notify\Notification;
use kalanis\kw_paths\Stored;
use kalanis\kw_semaphore\SemaphoreException;
use kalanis\kw_styles\Styles;


/**
 * Class Edit
 * @package KWCMS\modules\Menu
 * Site's short messages - edit form
 */
class Edit extends AAuthModule implements IModuleTitle
{
    use Lib\TMenu;
    use Templates\TModuleTemplate;

    /** @var Forms\EditNamesForm|null */
    protected $form = null;
    /** @var MapperException|null */
    protected $error = null;
    /** @var bool */
    protected $isProcessed = false;
    /** @var Forward */
    protected $forward = null;

    public function __construct()
    {
        $this->initTModuleTemplate(Stored::getPath());
        $this->initTMenu(Stored::getPath());
        $this->form = new Forms\EditNamesForm('editName');
        $this->forward = new Forward();
        $this->forward->setSource(new ServerRequest());
    }

    public function allowedAccessClasses(): array
    {
        return [IAccessClasses::CLASS_MAINTAINER, IAccessClasses::CLASS_ADMIN, IAccessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        try {
            $this->runTMenu($this->inputs, $this->user->getDir());

            $id = $this->checkedId();
            $this->form->composeForm($this->checkedEntry($id));
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                $this->libMenu->getMeta()->updateEntry(
                    $id,
                    strval($this->form->getControl('menuName')->getValue()),
                    strval($this->form->getControl('menuDesc')->getValue()),
                    boolval(intval($this->form->getControl('menuGoSub')->getValue()))
                );
                $this->libMenu->getMeta()->save();
                $this->libSemaphore->want();
                $this->isProcessed = true;
            }
        } catch (FormsException | MenuException | SemaphoreException $ex) {
            $this->error = $ex;
        }
    }

    /**
     * @return string
     * @throws MenuException
     */
    protected function checkedId(): string
    {
        $name = $this->getFromParam('id');
        if (empty($name)) {
            throw new MenuException(Lang::get('menu.error.item_not_found', $name));
        }
        return $name;
    }

    /**
     * @param string $name
     * @return Entry
     * @throws MenuException
     */
    protected function checkedEntry(string $name): Entry
    {
        $item = $this->libMenu->getMeta()->getEntry($name);
        if (empty($item)) {
            throw new MenuException(Lang::get('menu.error.item_not_found', $name));
        }
        return $item;
    }

    public function result(): Output\AOutput
    {
        return $this->isJson()
            ? $this->outJson()
            : $this->outHtml();
    }

    public function outHtml(): Output\AOutput
    {
        $out = new Output\Html();
        try {
            if ($this->error) {
                Notification::addError($this->error->getMessage());
            }
            if ($this->isProcessed) {
                Notification::addSuccess(Lang::get('menu.updated'));
            }
            $this->forward->forward($this->isProcessed || !empty($this->error));
            $editTmpl = new Templates\EditTemplate();
            Styles::want('Menu', 'menu.css');
            return $out->setContent($this->outModuleTemplate($editTmpl->setData($this->form, Lang::get('menu.update_texts'))->render()));
        } catch (FormsException $ex) {
            return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
        }
    }

    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } elseif (!$this->form->isValid()) {
            $out = new Output\JsonError();
            return $out->setContent(1, $this->form->renderErrorsArray());
        } else {
            $out = new Output\Json();
            return $out->setContent(['Success']);
        }
    }

    public function getTitle(): string
    {
        return Lang::get('menu.page') . ' - ' . Lang::get('menu.update_texts');
    }
}
