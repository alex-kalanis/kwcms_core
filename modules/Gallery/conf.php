<?php

################################################
#       GALLERY/CONF.PHP                       #
#       directory listing default settings     #
#       from Kalanys@2010                      #
#       EDITABLE FROM ADMIN                    #
#       License GNU/GPLv2                      #
################################################

$config = [
    'accept_types' => array('jpg','jpeg','gif','png','bmp'),
    'thumb'=>'.tmb', //STRING;help=dir with thumbs#
    'desc'=>'.txt', //STRING;help=dir with desc#
    'desc_maxlen'=>25, //INT;min=10;max=100;help=desc len before cut#
    'cols'=>3, //INT;min=1;max=10;help=num of dirs on line#
    'rows'=>4, //INT;min=1;max=10;help=num of lines#
    'style'=>'icon', //SET;values=icon|compact|list;help=listing type#
    'show_paging'=>true, //BOOLEAN;help=show dir pages#
];
