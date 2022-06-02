<?php

namespace kwcms;


use kalanis\kw_clipr\Tasks\ATask;
use kalanis\kw_connect\core\ConnectException;
use kalanis\kw_connect\search\Connector;
use kalanis\kw_mapper\MapperException;
use kalanis\kw_mapper\Search\Search;
use kalanis\kw_pager\BasicPager;
use kalanis\kw_paging\Positions;
use kalanis\kw_paging\Render;
use kalanis\kw_table\core\Table;
use kalanis\kw_table\core\TableException;
use kalanis\kw_table\output_cli\CliRenderer;
use KWCMS\modules\Short\Lib\MessageAdapter;
use KWCMS\modules\Short\ShortException;


/**
 * Class ListNews
 * @package clipr
 * @property string path
 * @property int page
 * @property int limit
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
     * @throws MapperException
     * @throws TableException
     */
    public function process(): void
    {
        $this->writeLn('<yellow><bluebg>+============================+</bluebg></yellow>');
        $this->writeLn('<yellow><bluebg>|           kwcms            |</bluebg></yellow>');
        $this->writeLn('<yellow><bluebg>+============================+</bluebg></yellow>');
        $this->writeLn('<yellow><bluebg>|    List news in storage    |</bluebg></yellow>');
        $this->writeLn('<yellow><bluebg>+============================+</bluebg></yellow>');

        $table = new Table();
        $render = new CliRenderer($table);
        $table->setOutput($render);

        // columns
        $table->addColumn('Title', new Table\Columns\Basic('title'));
        $table->addColumn('Date', new Table\Columns\Date('date'));
        $table->addColumn('Description', new Table\Columns\Basic('content'));

        // pager
        $pager = new BasicPager();
        $pager->setActualPage($this->page)->setLimit($this->limit);
        $table->addPager(new Render\CliPager(new Positions($pager)));

        // colors
        $render->getTableEngine()->setColors(['lgreen', 'magenta', '']);

        // data sources
        try {
            $adapter = new MessageAdapter($this->path);
            $table->addDataSetConnector(new Connector(new Search($adapter->getRecord())));
        } catch (ShortException $ex) {
            $this->sendErrorMessage(sprintf('No short messages in path *%s*', $this->path));
            return;
        }

        // render
        $this->writeln();
        $this->write($table->render());
    }
}
