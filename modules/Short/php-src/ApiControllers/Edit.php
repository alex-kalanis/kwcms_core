<?php

namespace KWCMS\modules\Short\ApiControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\ArrayAdapter;
use kalanis\kw_forms\Adapters\InputVarsAdapter;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Exceptions\RenderException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_mapper\Adapters\DataExchange;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_modules\Output;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_tree_controls\TWhereDir;
use kalanis\kw_user_paths\UserDir;
use KWCMS\modules\Core\Libs\AApiAuthModule;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Short\Lib;
use KWCMS\modules\Short\ShortException;


/**
 * Class Edit
 * @package KWCMS\modules\Short\ApiControllers
 * Site's short messages - edit form
 */
class Edit extends AApiAuthModule
{
    use TWhereDir;

    /** @var Lib\MessageForm */
    protected $form = null;
    /** @var MapperException */
    protected $error = null;
    /** @var UserDir */
    protected $userDir = null;
    /** @var CompositeAdapter */
    protected $files = null;
    /** @var bool */
    protected $isProcessed = false;

    /**
     * @param mixed ...$constructParams
     * @throws ConfException
     * @throws FilesException
     * @throws LangException
     * @throws PathsException
     */
    public function __construct(...$constructParams)
    {
        Lang::load('Short');
        Lang::load('Admin');
        Config::load('Short');
        $this->whereConst = 'target';
        $this->form = new Lib\MessageForm('editMessage');
        $this->userDir = new UserDir(new Lib\Translations());
        $this->files = (new Factory(new FilesTranslations()))->getClass($constructParams);
    }

    public function allowedAccessClasses(): array
    {
        return [IProcessClasses::CLASS_MAINTAINER, IProcessClasses::CLASS_ADMIN, IProcessClasses::CLASS_USER, ];
    }

    public function run(): void
    {
        $this->initWhereDir(new ArrayAdapter([]), $this->inputs);
        $this->userDir->setUserPath($this->user->getDir());

        try {
            $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
            $currentPath = Stuff::linkToArray($this->getWhereDir());

            $adapter = new Lib\MessageAdapter($this->files, array_merge($userPath, $currentPath));
            $record = $adapter->getRecord();
            $record->id = strval($this->getFromParam('id'));
            $record->load();
            $this->form->composeForm($record);
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                $ex = new DataExchange($record);
                if ((bool)$ex->import($this->form->getValues())) {
                    $this->isProcessed = $record->save();
                }
            }
        } catch (ConfException | FilesException | FormsException | MapperException | PathsException | ShortException $ex) {
            $this->error = $ex;
        }
    }

    /**
     * @throws RenderException
     * @return Output\AOutput
     */
    public function result(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } elseif (!$this->form->isValid()) {
            $out = new Output\Json();
            return $out->setContent($this->form->renderErrorsArray());
        } else {
            $out = new Output\Json();
            return $out->setContent(['Success', intval($this->isProcessed)]);
        }
    }
}
