<?php

################################################
#       VIDEO/CONF.PHP                         #
#       directory listing default settings     #
#       from Kalanys@2010                      #
#       EDITABLE FROM ADMIN                    #
#       License GNU/GPLv2                      #
################################################

$config = [
    'accept_types' => array('avi','divx','mid','mov','mp3','mp4','mpg','mpeg','ogg','qt','ra','rm','wav','webm','wmv'),
    'width'=>500, //INT;min=100;max=1000;help=player width#
    'height'=>395, //INT;min=100;max=1000;help=player height#
    'icon_width'=>120, //INT;min=50;max=500;help=icon width#
    'icon_height'=>80, //INT;min=50;max=500;help=icon height#
    'thumb'=>'.tmb', //STRING;help=dir with thumbs#
    'desc'=>'.txt', //STRING;help=dir with desc#
    'desc_maxlen'=>25, //INT;min=10;max=100;help=desc len before cut#
    'cols'=>3, //INT;min=1;max=10;help=num of dirs on line#
    'rows'=>4, //INT;min=1;max=10;help=num of lines#
    'style'=>'list', //SET;values=icon|compact|list;help=listing type#
    'show_paging'=>true, //BOOLEAN;help=show dir pages#
];
