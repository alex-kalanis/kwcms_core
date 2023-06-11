<?php

######################################
#       PAGE.PHP                     #
#       page settings                #
#       from Kalanys@2013            #
#       EDITABLE FROM ADMIN          #
######################################

$config = [
    'page.use_cache'=>false, //BOOLEAN;help=pages can use cache#
    'page.more_lang'=>false, //BOOLEAN;help=more languages#
    'page.default_lang'=>'cze', //FILELIST;path=/lang;help=default language#
    'page.use_lang'=>'cze', //DIRLIST;path=~;help=use language#
    'page.encoding_lang'=>'cz', //STRING;help=encoding by W3C#
    'page.default_style'=>'atonika', //DIRLIST;path=/style;help=default style#
    'page.default_user'=>'data', //DIRLIST;path=/users;help=default user#
    'page.system_prefix'=>true, //BOOLEAN;help=use users directory as path prefix#
    'page.image_prefix'=>'', //STRING;help=prefix for directory lookup other than users#
    'page.data_separator'=>false, //BOOLEAN;help=use data directory as infix between user and path#
    'page.site_name'=>'Kiosek Master', //STRING;help=name of site#
    'page.page_title'=>'Kiosková spojka', //STRING;help=title of site#
    'page.contact'=>'nikam&#x0040;me.cz', //STRING;help=contact to owner#
    'page.keywords'=>'keyword, keywords, KWCMS3', //STRING;help=keywords#
    'page.about'=>'nic ke scanování', //STRING;help=about pages#
    'page.files_per_page'=>24, //INT;min=1;max=100;help=files per page#
    'page.files_in_col'=>4, //INT;min=1;max=10;help=files in column#
    'page.files_in_row'=>6, //INT;min=1;max=10;help=files on line#
    'page.admin_background'=>'/web/ms:sysimage/system/background.png', //STRING;help=admin background#
];
