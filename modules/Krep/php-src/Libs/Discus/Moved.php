<?php

namespace KWCMS\modules\Krep\Libs\Discus;


use KWCMS\modules\Krep\Libs\Shared\PageData;


/**
 * Class Moved
 * @package KWCMS\modules\Krep\Libs\Discus
 */
class Moved
{
    public function process(string $remoteData, string $host, string $script): PageData
    {
        $addr = $this->getAddr($remoteData);
        header("HTTP/1.1 302 Moved Permanently");
        header("Location: http://" . $host . $script . '?addr=' . $addr);
        $data = new PageData();
        $data->die = true;
        return $data;
    }

    protected function getAddr(string $head): string
    {
        $w = explode("\r\n", $head);
        $c = true;
        $l = '';
        foreach ($w as $i => $v) {
            if ($c) {
                $d = explode(":", $v, 2);
                if (strpos(" " . $d[0], 'Location')) {
                    $l = substr($d[1], 1);
                    $c = false;
                }
//print_r(array("v"=>$v,"d"=>$d,"r"=>(int)strpos($d[0],'Location')));
            }
        }
//print_r(array("l"=>$l));
        return $l;
    }
}
