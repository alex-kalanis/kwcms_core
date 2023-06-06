<?php

namespace KWCMS\modules\File\Lib;


use kalanis\kw_files\Access\CompositeAdapter;
use kalanis\kw_files\FilesException;
use kalanis\kw_files\Traits\TToString;
use kalanis\kw_modules\Output\AOutput;
use kalanis\kw_paths\PathsException;
use KWCMS\modules\File\Lib\SizeAdapters\AAdapter;


/**
 * Class Output
 * @package KWCMS\modules\File\Lib
 * Users files - dump output
 * Must go around the limitation of real output - have its own write into response data stream
 */
class Output extends AOutput
{
    use TToString;

    /** @var CompositeAdapter */
    protected $files = null;
    /** @var AAdapter|null */
    protected $sizeAdapter = null;
    /** @var string */
    protected $name = '';
    /** @var string[] */
    protected $path = [];

    public function __construct(CompositeAdapter $files, string $name, array $path)
    {
        $this->files = $files;
        $this->path = $path;
        $this->name = $name;
    }

    public function setAdapter(AAdapter $adapter): self
    {
        $this->sizeAdapter = $adapter;
        return $this;
    }

    /**
     * @throws FilesException
     * @throws PathsException
     * @return string
     */
    public function output(): string
    {
        if ($this->sizeAdapter && $this->sizeAdapter->getMax()) {
            // is from what file
            header('Content-Length: ' . $this->sizeAdapter->getUsableLength());

            $cur = $this->sizeAdapter->getStart();

            while ($this->sizeAdapter->canContinue($cur) && (CONNECTION_NORMAL == connection_status())) {
                // reset time limit for big files
                set_time_limit(0);
                print $this->toString($this->name, $this->files->readFile(
                    $this->path,
                    $cur,
                    $this->sizeAdapter->readLength($cur)
                ));
                if ($this->sizeAdapter->flush()) {
                    flush();
                    ob_flush();
                }
                $cur += $this->sizeAdapter->getStepBy();
            };
            return '';
        }
        return $this->toString($this->name, $this->files->readFile($this->path));
    }
}
