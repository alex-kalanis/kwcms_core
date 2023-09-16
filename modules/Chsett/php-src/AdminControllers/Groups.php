<?php

namespace KWCMS\modules\Chsett\AdminControllers;


use kalanis\kw_accounts\AccountsException;
use kalanis\kw_accounts\Interfaces;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_langs\Lang;
use kalanis\kw_langs\LangException;
use kalanis\kw_modules\Output;
use kalanis\kw_table\core\TableException;
use KWCMS\modules\Chsett\Lib;
use KWCMS\modules\Chsett\Templates;
use KWCMS\modules\Core\Interfaces\Modules\IHasTitle;
use KWCMS\modules\Core\Libs\AAuthModule;
use KWCMS\modules\Core\Libs\ExternalLink;


/**
 * Class Groups
 * @package KWCMS\modules\Chsett\AdminControllers
 * Site's Groups - list available ones
 */
class Groups extends AAuthModule implements IHasTitle
{
    use Templates\TModuleTemplate;

    /** @var Interfaces\IProcessGroups|null */
    protected $libGroups = null;

    /**
     * @param Interfaces\IProcessGroups $groups
     * @param ExternalLink $external
     * @throws LangException
     */
    public function __construct(
        Interfaces\IProcessGroups $groups,
        ExternalLink $external
    ) {
        $this->initTModuleTemplate($external);
        $this->libGroups = $groups;
    }

    public function allowedAccessClasses(): array
    {
        return [Interfaces\IProcessClasses::CLASS_MAINTAINER, ];
    }

    public function run(): void
    {
    }

    /**
     * @throws AccountsException
     * @return Output\AOutput
     */
    public function result(): Output\AOutput
    {
        return $this->isJson()
            ? $this->outJson()
            : $this->outHtml();
    }

    public function outHtml(): Output\AOutput
    {
        $out = new Output\Html();
        if (!empty($this->error)) {
            return $out->setContent($this->outModuleTemplate($this->error->getMessage() . nl2br($this->error->getTraceAsString())));
        }
        try {
            $table = new Lib\GroupTable($this->inputs, $this->links, $this->libGroups, $this->user);
            return $out->setContent($this->outModuleTemplate($table->getTable()->render()));
        } catch ( AccountsException | ConnectException | FormsException | LangException | TableException $ex) {
            return $out->setContent($this->outModuleTemplate($ex->getMessage() . nl2br($ex->getTraceAsString())));
        }
    }

    /**
     * @throws AccountsException
     * @return Output\AOutput
     */
    public function outJson(): Output\AOutput
    {
        if ($this->error) {
            $out = new Output\JsonError();
            return $out->setContent($this->error->getCode(), $this->error->getMessage());
        } else {
            $out = new Output\Json();
            $out->setContent([
                'groups' => $this->libGroups->readGroup(),
            ]);
            return $out;
        }
    }

    public function getTitle(): string
    {
        return Lang::get('chsett.page');
    }
}
