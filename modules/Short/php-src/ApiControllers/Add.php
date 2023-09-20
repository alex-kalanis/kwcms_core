<?php

namespace KWCMS\modules\Short\AdminControllers;


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
use kalanis\kw_input\Simplified\SessionAdapter;
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
 * Class Add
 * @package KWCMS\modules\Short\AdminControllers
 * Site's short messages - add form
 */
class Add extends AApiAuthModule
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
        Config::load('Short');
        Lang::load('Short');
        Lang::load('Admin');
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
            $this->form->composeForm(new Lib\ShortMessage()); // must be without file!!!
            $this->form->setInputs(new InputVarsAdapter($this->inputs));
            if ($this->form->process()) {
                $this->initWhereDir(new SessionAdapter(), $this->inputs);
                try {
                    $record = $adapter->getRecord();
                } catch (ShortException $ex) { // create file when not exists
                    $adapter->createRecordFile();
                    $record = $adapter->getRecord();
                }
                $ex = new DataExchange($record);
                if ((bool)$ex->import($this->form->getValues())) {
                    $record->date = time();
                    $this->isProcessed = $record->save(true);
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
