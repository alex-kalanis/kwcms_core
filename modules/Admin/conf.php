<?php

######################################
#       ADMIN.PHP                    #
#       admin settings               #
#       from Kalanys@2010            #
#       EDITABLE FROM ADMIN          #
######################################

$config = [
    # passwd
    "admin.salt"=>'jly!pk$HU6b%BHqrFeB#?kEdN#sYy8FQsoK2u.kbz.!!Edb:3GBaiDr/',
    "admin.passfn"=>"hash",
    "admin.passtp"=>"sha256",
    "admin.login_max_time"=>2400, //INT;help=In seconds# in UNIX time (in sec)
    "admin.max_log_count"=>50, //INT;help=max login tryies before ban# how many times can user try log-in before session ban
    "admin.max_susr"=>3, //INT;help=maximum of super users#
    "admin.use_rewrite"=>true,//BOOL;help=rewrite modules paths#
    "admin.web_dir"=>"", //STRING;help=dir with installated system#
    "admin.charset"=>"utf-8", //FILELIST;path=/admin/base/charsets;help=encoding file#
    "admin.captcha_length"=>6, //INT;help=length of captcha#
    "admin.image_size_x"=>200, //INT;help=captcha size#
    "admin.image_size_y"=>100, //INT;help=captcha size#
    "admin.background"=>'/web/ms:sysimage/system/background.png', //STRING;help=admin background#
    "admin.lang"=>'cze', //STRING;help=admin translation#
    "admin.style"=>'default', //STRING;path=/style;help=admin style#
    "admin.script"=>false, //BOOLEAN;help=enable scripting in admin#
];
