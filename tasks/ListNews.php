<?php

namespace kwcms;


use kalanis\kw_address_handler\Handler;
use kalanis\kw_address_handler\HandlerException;
use kalanis\kw_address_handler\Sources\Inputs;
use kalanis\kw_clipr\Tasks\ATask;
use kalanis\kw_confs\ConfException;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_connect\search\Connector;
use kalanis\kw_files\Access;
use kalanis\kw_files\FilesException;
use kalanis\kw_forms\Adapters;
use kalanis\kw_forms\Exceptions\FormsException;
use kalanis\kw_forms\Form;
use kalanis\kw_input\Interfaces\IEntry;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_pager\BasicPager;
use kalanis\kw_paging\Positions;
use kalanis\kw_paging\Render;
use kalanis\kw_paths\PathsException;
use kalanis\kw_paths\Stuff;
use kalanis\kw_table\core\Table;
use kalanis\kw_table\core\TableException;
use kalanis\kw_table\form_kw\KwFilter;
use kalanis\kw_table\form_nette\Fields\TextContains;
use kalanis\kw_table\output_cli\CliRenderer;
use KWCMS\modules\Core\Libs\FilesTranslations;
use KWCMS\modules\Short\Lib\MessageAdapter;
use KWCMS\modules\Short\ShortException;


/**
 * Class ListNews
 * @package clipr
 * @property string $path
 * @property int $page
 * @property int $limit
 */
class ListNews extends ATask
{
    public function __construct()
    {
        if (class_exists('\kalanis\kw_autoload\Autoload')) {
            \kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%5$s%1$sphp-src%1$s%6$s');
            \kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%5$s%1$ssrc%1$s%6$s');
            \kalanis\kw_autoload\Autoload::addPath('%2$s%1$smodules%1$s%5$s%1$s%6$s');
        }
    }

    protected function startup(): void
    {
        parent::startup();
        $this->params->addParam('path', 'path', null, '', null, 'Specify own path to tasks');
        $this->params->addParam('page', 'page', null, 1, null, 'Specify which page will be shown');
        $this->params->addParam('limit', 'limit', null, 10, null, 'Specify how many entries will be shown');
    }

    public function desc(): string
    {
        return 'Render list of news in preselected directory';
    }

    /**
     * @throws ConnectException
     * @throws FormsException
     * @throws HandlerException
     * @throws MapperException
     * @throws TableException
     * @return int
     */
    public function process(): int
    {
        $this->writeLn('<yellow><bluebg>+============================+</bluebg></yellow>');
        $this->writeLn('<yellow><bluebg>|           kwcms            |</bluebg></yellow>');
        $this->writeLn('<yellow><bluebg>+============================+</bluebg></yellow>');
        $this->writeLn('<yellow><bluebg>|    List news in storage    |</bluebg></yellow>');
        $this->writeLn('<yellow><bluebg>+============================+</bluebg></yellow>');

        $table = new Table();
        $render = new CliRenderer($table);
        $table->setOutput($render);

        $inputVariables = new Adapters\InputVarsAdapter($this->inputs);
        $form = new Form('newsForm');
        $form->setMethod(IEntry::SOURCE_CLI);
        $table->addHeaderFilter(new KwFilter($form));
        $form->setInputs($inputVariables);

        $table->addOrder(new Table\Order(new Handler(new Inputs($this->inputs))));

        // columns
        $table->addOrderedColumn('Title', new Table\Columns\Basic('title'), new TextContains());
        $table->addOrderedColumn('Date', new Table\Columns\Date('date'));
        $table->addOrderedColumn('Description', new Table\Columns\Basic('content'), new TextContains());

        // pager
        $pager = new BasicPager();
        $pager->setActualPage($this->page)->setLimit($this->limit);
        $table->addPager(new Render\CliPager(new Positions($pager)));

        // colors
        $render->getTableEngine()->setColors(['lgreen', 'magenta', '']);

        // data sources
        try {
            $files = (new Access\Factory(new FilesTranslations()))->getClass(
                realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR)
            );
            $adapter = new MessageAdapter($files, Stuff::pathToArray($this->path));
            $table->addDataSetConnector(new Connector(new Search($adapter->getRecord())));

        } catch (ShortException $ex) {
            $this->sendErrorMessage(sprintf('No short messages in path *%s*', $this->path));
            return static::STATUS_NO_INPUT_FILE;

        } catch (ConfException | FilesException | PathsException $ex) {
            $this->sendErrorMessage($ex->getMessage());
            return static::STATUS_BAD_CONFIG;
        }

        // render
        $this->writeln();
        $this->write($table->render());
        return static::STATUS_SUCCESS;
    }
}
