<?php

namespace KWCMS\modules\File;


use kalanis\kw_modules\Output\AOutput;


/**
 * Class Output
 * @package KWCMS\modules\File
 * Users files - dump output
 * Must go around the limitation of real output - have its own write into response data stream
 */
class Output extends AOutput
{
    /** @var null|Seek */
    protected $seek = null;

    public function setSeek(Seek $seek): self
    {
        $this->seek = $seek;
        return $this;
    }

    public function output(): string
    {
        if ($this->seek && $this->seek->getFilePath() && $this->seek->getMax()) {
            // is from what file
            header('Content-Length: ' . $this->seek->getUsableLength());

            $fp = fopen($this->seek->getFilePath(), 'rb');
            $cur = $this->seek->getStart();
            fseek($fp, $cur, SEEK_SET);

            while ((!feof($fp)) && ($cur <= $this->seek->getEnd()) && (CONNECTION_NORMAL == connection_status())) {
                // reset time limit for big files
                set_time_limit(0);
                print fread($fp, min($this->seek->getStepBy(), ($this->seek->getEnd() - $cur) + 1));
                if ($this->seek->flush()) {
                    flush();
                    ob_flush();
                }
                $cur += $this->seek->getStepBy();
            };
            fclose($fp);
        }
        return '';
    }
}
