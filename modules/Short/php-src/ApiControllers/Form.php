<?php

namespace KWCMS\modules\Short\ApiControllers;


use kalanis\kw_accounts\Interfaces\IProcessClasses;
use kalanis\kw_confs\ConfException;
use kalanis\kw_confs\Config;
use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\Access\Factory;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters\ArrayAdapter;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
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
 * Class Form
 * @package KWCMS\modules\Short\ApiControllers
 * Site's short messages - how is form formatted
 */
class Form extends AApiAuthModule
{
    use TWhereDir;

    /** @var Lib\ShortMessage */
    protected $record = null;
    /** @var Lib\MessageForm */
    protected $form = null;
    /** @var MapperException */
    protected $error = null;
    /** @var UserDir */
    protected $userDir = null;
    /** @var CompositeAdapter */
    protected $files = null;

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
            $id = $this->getFromParam('id');
            if (!empty($id)) {
                $userPath = array_values($this->userDir->process()->getFullPath()->getArray());
                $currentPath = Stuff::linkToArray($this->getWhereDir());

                $adapter = new Lib\MessageAdapter($this->files, array_merge($userPath, $currentPath));
                $record = $adapter->getRecord();
                $record->id = strval($id);
                $record->load();
            } else {
                $record = new Lib\ShortMessage();
            }
            $this->record = $record;
            $this->form->composeForm($record);

        } catch (ConfException | FilesException | MapperException | PathsException | ShortException $ex) {
            $this->error = $ex;
        }
    }

    public function result(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            $title = $this->form->getControl('title');
            $content = $this->form->getControl('content');
            $send = $this->form->getControl('postMessage');
            $reset = $this->form->getControl('clearMessage');
            return $out->setContent([
                'form' => $this->form->getLabel(),
                'controls' => [
                    [
                        'type' => 'text',
                        'key' => $title->getKey(),
                        'value' => strval($this->record->title),
                        'label' => $title->getLabel(),
                        'must_be_set' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'key' => $content->getKey(),
                        'value' => strval($this->record->content),
                        'label' => $content->getLabel(),
                        'must_be_set' => true,
                    ],
                    [
                        'type' => 'submit',
                        'key' => $send->getKey(),
                        'value' => $send->getValue(),
                        'label' => $send->getLabel(),
                    ],
                    [
                        'type' => 'reset',
                        'key' => $reset->getKey(),
                        'value' => $reset->getValue(),
                        'label' => $reset->getLabel(),
                    ],
                ]
            ]);
        }
    }
}
