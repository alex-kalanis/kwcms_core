<?php

namespace KWCMS\modules\File\Lib;


use kalanis\kw_modules\Output\AOutput;
use KWCMS\modules\File\Lib\SizeAdapters\AAdapter;


/**
 * Class Output
 * @package KWCMS\modules\File\Lib
 * Users files - dump output
 * Must go around the limitation of real output - have its own write into response data stream
 */
class Output extends AOutput
{
    /** @var AAdapter|null */
    protected $sizeAdapter = null;

    public function setAdapter(AAdapter $adapter): self
    {
        $this->sizeAdapter = $adapter;
        return $this;
    }

    public function output(): string
    {
        if ($this->sizeAdapter && $this->sizeAdapter->getFilePath() && $this->sizeAdapter->getMax()) {
            // is from what file
            header('Content-Length: ' . $this->sizeAdapter->getUsableLength());

            $fp = fopen($this->sizeAdapter->getFilePath(), 'rb');
            $cur = $this->sizeAdapter->getStart();
            fseek($fp, $cur, SEEK_SET);

            while ((!feof($fp)) && $this->sizeAdapter->canContinue($cur) && (CONNECTION_NORMAL == connection_status())) {
                // reset time limit for big files
                set_time_limit(0);
                print fread($fp, $this->sizeAdapter->readLength($cur));
                if ($this->sizeAdapter->flush()) {
                    flush();
                    ob_flush();
                }
                $cur += $this->sizeAdapter->getStepBy();
            };
            fclose($fp);
        }
        return '';
    }
}
