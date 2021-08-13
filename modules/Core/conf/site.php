<?php

######################################
#       SITE.PHP                     #
#       site settings                #
#       from Kalanys@2013            #
#       EDITABLE FROM ADMIN          #
######################################

$config = [
    "site.more_users"=>false, //BOOLEAN;help=more users#
    "site.use_rewrite"=>true, //BOOLEAN;help=rewriting links#
    "site.recode_from"=>"latin_utf8", #//FILELIST;path=/admin/base/charsets;help=recoding entities set#
//    "site.server_path"=>"w2/", //STRING;help=path to server#
    "site.basedir_path"=>"data/", //STRING;help=path to basic dir#
    "site.fake_dir"=>"web/", //STRING;help=virtual dir for rewrite#
    "site.contact"=>'Somewhere', //STRING;help=mail to admin#
    "site.far_debug"=>true, //BOOLEAN;help=debugging from far machine#
    "site.debug"=>false //BOOLEAN;help=debugging output#
];
