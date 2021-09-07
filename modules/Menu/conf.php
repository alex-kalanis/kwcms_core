<?php

######################################
#       MENU/CONF.PHP                #
#       menu default settings        #
#       from Kalanys@2010            #
#       EDITABLE FROM ADMIN          #
######################################

$config = [
    "use_cache"=>false, //BOOLEAN;help=use cache or regenerate menu every time#
    "meta"=>"index.meta", //STRING;help=file with menu metadata# file with description of dir
    "max_sub_number"=>255, //INT;help=maximal count of sub-dirs#
    "meta_regen"=>"action.meta", //STRING;help=cache file with menu#
];
