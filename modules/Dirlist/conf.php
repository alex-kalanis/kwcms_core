<?php

################################################
#       DIRLIST/CONF.PHP                       #
#       directory listing default settings     #
#       from Kalanys@2010                      #
#       EDITABLE FROM ADMIN                    #
#       License GNU/GPLv2                      #
################################################

$config = [
#    "deny_types" => array("php","htm","html","htx","htt","asp","js","vbs","pl","meta","short","cache","ini","conf"),
    "deny_types" => array("php","htx","htt","asp","js","vbs","pl","meta","short","cache","ini","conf"),
    "thumb"=>'.tmb', //STRING;help=dir with thumbs#
    "desc"=>'.txt', //STRING;help=dir with desc#
    "desc_maxlen"=>25, //INT;min=10;max=100;help=desc len before cut#
    "cols"=>3, //INT;min=1;max=10;help=num of dirs on line#
    "rows"=>4, //INT;min=1;max=10;help=num of lines#
    "style"=>"list", //SET;values=icon|compact|list;help=listing type#
    "show_paging"=>true, //BOOLEAN;help=show dir pages#
];
